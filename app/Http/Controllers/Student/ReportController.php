<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MonthlyReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $month = $this->resolveMonth($request, $user);
        $report = new MonthlyReport($user, $month);

        $current = now()->startOfMonth();
        $first = Carbon::parse($user->created_at)->startOfMonth();

        $previous = $report->previousTotal();
        $total = $report->total();

        return view('student.reports', [
            'monthLabel' => $month->format('F Y'),
            'monthKey'   => $month->format('Y-m'),
            'prevKey'    => $month->gt($first) ? $month->copy()->subMonthNoOverflow()->format('Y-m') : null,
            'nextKey'    => $month->lt($current) ? $month->copy()->addMonthNoOverflow()->format('Y-m') : null,
            'isCurrent'  => $month->isSameMonth($current),

            'report'     => $report,
            'weeks'      => $report->byWeek(),
            'categories' => $report->byCategory(),
            'top'        => $report->topExpenses(),

            'vsLastMonth' => [
                'has_data' => $previous > 0,
                'percent'  => $previous > 0 ? (int) round(($total - $previous) / $previous * 100) : 0,
                'diff'     => abs($total - $previous),
                'less'     => $total < $previous,
            ],
        ]);
    }

    /** Download the month's expenses as a CSV file (opens in Excel / Google Sheets). */
    public function export(Request $request)
    {
        $user = $request->user();
        $month = $this->resolveMonth($request, $user);
        $report = new MonthlyReport($user, $month);

        return response()->streamDownload(function () use ($report) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // so Excel reads the file as UTF-8

            fputcsv($out, ['Date', 'Title', 'Category', 'Amount (PHP)'], ',', '"', '');

            foreach ($report->expenses()->reverse() as $expense) {
                fputcsv($out, [
                    $expense->spent_on->toDateString(),
                    $this->safe($expense->title),
                    $this->safe($expense->category),
                    number_format((float) $expense->amount, 2, '.', ''),
                ], ',', '"', '');
            }

            fclose($out);
        }, 'estudyantipid-' . $month->format('Y-m') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /** ?month=2026-10, kept between the month the student joined and this month. */
    private function resolveMonth(Request $request, User $user): Carbon
    {
        $current = now()->startOfMonth();
        $first = Carbon::parse($user->created_at)->startOfMonth();
        $raw = $request->string('month')->toString();

        $month = $current;
        if (preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $raw, $m)) {
            $month = Carbon::create((int) $m[1], (int) $m[2], 1)->startOfDay();
        }

        if ($month->gt($current)) {
            $month = $current;
        }
        if ($month->lt($first)) {
            $month = $first;
        }

        return $month;
    }

    /** Stops a title like "=SUM(...)" from running as a formula when the CSV is opened in Excel. */
    private function safe(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
    }
}
