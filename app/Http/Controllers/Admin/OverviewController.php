<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Message;
use App\Models\SavingsDeposit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OverviewController extends Controller
{
    public function __invoke()
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // ---- Top cards ----
        $students = User::where('role', 'student')->count();
        $newThisMonth = User::where('role', 'student')->where('created_at', '>=', now()->startOfMonth())->count();

        // Average savings rate: this week's savings deposits, as a share of what every student could have been given.
        $allowance = (float) Category::sum('weekly_limit');
        $savedThisWeek = (float) SavingsDeposit::whereDate('deposited_on', '>=', $weekStart->toDateString())
            ->whereDate('deposited_on', '<=', $weekEnd->toDateString())
            ->sum('amount');
        $savingsRate = ($students > 0 && $allowance > 0)
            ? (int) round($savedThisWeek / ($students * $allowance) * 100)
            : 0;

        // ---- Campus spend by day (all students, this week) ----
        $byDate = DB::table('expenses')
            ->whereDate('spent_on', '>=', $weekStart->toDateString())
            ->whereDate('spent_on', '<=', $weekEnd->toDateString())
            ->selectRaw('DATE(spent_on) as d, SUM(amount) as total')
            ->groupByRaw('DATE(spent_on)')
            ->pluck('total', 'd');

        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $days[] = [
                'day'    => $date->format('D'),
                'amount' => (float) ($byDate[$date->toDateString()] ?? 0),
            ];
        }

        $max = max(array_column($days, 'amount'));

        return view('admin.overview', [
            'students'     => $students,
            'newThisMonth' => $newThisMonth,
            'expenseCount' => Expense::count(),
            'unread'       => Message::whereNull('read_at')->count(),
            'savingsRate'  => $savingsRate,
            'days'         => $days,
            'max'          => $max,
            'top'          => $this->niceTop($max),
            'activity'     => ActivityLog::latest()->latest('id')->take(4)->get(),
        ]);
    }

    /** Round the chart's top up to a tidy number: 520 -> 600, 4,520 -> 5,000. */
    private function niceTop(float $max): int
    {
        if ($max <= 0) {
            return 100;
        }

        $magnitude = 10 ** (strlen((string) (int) ceil($max)) - 1);

        return (int) (ceil($max / $magnitude) * $magnitude);
    }
}
