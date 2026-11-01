<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\WeeklySummary;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __invoke(Request $request)
    {
        $summary = new WeeklySummary($request->user());

        $daily = $summary->dailyTotals();
        $total = $summary->spentTotal();

        // Biggest day (null when nothing has been spent yet)
        $biggest = collect($daily)->sortByDesc('amount')->first();
        if ($biggest && $biggest['amount'] <= 0) {
            $biggest = null;
        }

        // Top category and its share of this week's spending
        $byCategory = $summary->spentByCategory()->sortDesc();
        $topCategory = null;
        if ($total > 0 && $byCategory->isNotEmpty()) {
            $topCategory = [
                'name'    => $byCategory->keys()->first(),
                'percent' => (int) round($byCategory->first() / $total * 100),
            ];
        }

        // This week compared with last week
        $last = $summary->spentLastWeek();
        $vsLastWeek = [
            'has_data' => $last > 0,
            'percent'  => $last > 0 ? (int) round(($total - $last) / $last * 100) : 0,
            'diff'     => abs($total - $last),
            'less'     => $total < $last,
        ];

        return view('student.analytics', [
            'daily'       => $daily,
            'biggest'     => $biggest,
            'topCategory' => $topCategory,
            'vsLastWeek'  => $vsLastWeek,
        ]);
    }
}
