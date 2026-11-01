<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\SavingsGoal;
use App\Services\WeeklySummary;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $summary = new WeeklySummary($user);

        $left = max(0, $summary->left());

        // Savings: total across all goals, and the progress of the goal closest to being done.
        $goals = SavingsGoal::where('user_id', $user->id)->get();
        $focus = $goals->filter(fn ($g) => ! $g->isComplete())->sortByDesc(fn ($g) => $g->percent())->first();

        $recent = Expense::where('user_id', $user->id)
            ->orderByDesc('spent_on')
            ->orderByDesc('id')
            ->take(3)
            ->get()
            ->map(fn ($e) => [
                'title'    => $e->title,
                'category' => $e->category,
                'date'     => $e->spent_on->format('M j'),
                'amount'   => (float) $e->amount,
            ]);

        return view('student.dashboard', [
            'firstName' => explode(' ', trim($user->name))[0],
            'weekStart' => $summary->weekStart()->format('M j'),

            'stats' => [
                'allowance'      => $summary->allowance(),
                'left'           => $left,
                'spent_today'    => $summary->spentToday(),
                'expenses_today' => $summary->expenseCountToday(),
                'safe_spend'     => (int) floor($left / $summary->daysLeft()),
                'saved'          => (float) $goals->sum('saved_amount'),
                'goal_name'      => $focus?->name,
                'goal_progress'  => $focus ? (int) round($focus->percent()) : 0,
            ],

            'weekly'  => $summary->dailyTotals(),
            'budgets' => $summary->budgets(),
            'recent'  => $recent,
        ]);
    }
}
