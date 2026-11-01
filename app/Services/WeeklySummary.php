<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\SavingsDeposit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * One place that works out "this week" (Monday to Sunday) for a student,
 * so the Dashboard, Budgets and Analytics pages always agree.
 */
class WeeklySummary
{
    /** At or above this percent a spending budget shows "Near limit". */
    public const NEAR_LIMIT = 85;

    private Collection $expenses;

    public function __construct(private User $user)
    {
        $this->expenses = Expense::where('user_id', $user->id)
            ->whereBetween('spent_on', [
                $this->weekStart()->toDateString(),
                $this->weekEnd()->toDateString(),
            ])
            ->orderByDesc('spent_on')
            ->orderByDesc('id')
            ->get();
    }

    public function weekStart(): Carbon
    {
        return now()->startOfWeek();
    }

    public function weekEnd(): Carbon
    {
        return now()->endOfWeek();
    }

    /** This week's expenses, newest first. */
    public function expenses(): Collection
    {
        return $this->expenses;
    }

    public function spentTotal(): float
    {
        return (float) $this->expenses->sum('amount');
    }

    public function spentToday(): float
    {
        return (float) $this->expenses->filter(fn ($e) => $e->spent_on->isToday())->sum('amount');
    }

    public function expenseCountToday(): int
    {
        return $this->expenses->filter(fn ($e) => $e->spent_on->isToday())->count();
    }

    /** @return Collection<string, float> category => pesos spent */
    public function spentByCategory(): Collection
    {
        return $this->expenses->groupBy('category')->map(fn ($g) => (float) $g->sum('amount'));
    }

    /** Seven entries, Monday to Sunday: ['day' => 'Mon', 'name' => 'Monday', 'amount' => 210.0] */
    public function dailyTotals(): array
    {
        $byDate = $this->expenses
            ->groupBy(fn ($e) => $e->spent_on->toDateString())
            ->map(fn ($g) => (float) $g->sum('amount'));

        $start = $this->weekStart();
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);

            $days[] = [
                'day'    => $date->format('D'),
                'name'   => $date->format('l'),
                'amount' => (float) $byDate->get($date->toDateString(), 0.0),
            ];
        }

        return $days;
    }

    public function spentLastWeek(): float
    {
        return (float) Expense::where('user_id', $this->user->id)
            ->whereBetween('spent_on', [
                now()->subWeek()->startOfWeek()->toDateString(),
                now()->subWeek()->endOfWeek()->toDateString(),
            ])
            ->sum('amount');
    }

    /** Pesos put into savings goals this week. */
    public function savedThisWeek(): float
    {
        return (float) SavingsDeposit::where('user_id', $this->user->id)
            ->whereBetween('deposited_on', [
                $this->weekStart()->toDateString(),
                $this->weekEnd()->toDateString(),
            ])
            ->sum('amount');
    }

    /** Weekly allowance = all the budget limits added together (spending + savings). */
    public function allowance(): float
    {
        return (float) array_sum(config('estudyantipid.budgets')) + (float) config('estudyantipid.savings_budget');
    }

    public function left(): float
    {
        return $this->allowance() - $this->spentTotal();
    }

    /** Whole days still to go this week, counting today (Monday = 7 ... Sunday = 1). */
    public function daysLeft(): int
    {
        return 8 - now()->dayOfWeekIso;
    }

    /**
     * The four budget cards: three spending budgets plus Savings.
     * status: 'ok' | 'near' | 'over' (Savings is always 'ok': filling it up is good).
     */
    public function budgets(): array
    {
        $spent = $this->spentByCategory();
        $cards = [];

        foreach (config('estudyantipid.budgets') as $category => $limit) {
            $cards[] = $this->card($category, (float) $spent->get($category, 0), (float) $limit, true);
        }

        $cards[] = $this->card('Savings', $this->savedThisWeek(), (float) config('estudyantipid.savings_budget'), false);

        return $cards;
    }

    private function card(string $category, float $used, float $limit, bool $isSpending): array
    {
        $percent = $limit > 0 ? (int) round($used / $limit * 100) : 0;

        $status = 'ok';
        if ($isSpending) {
            $status = match (true) {
                $percent > 100              => 'over',
                $percent >= self::NEAR_LIMIT => 'near',
                default                     => 'ok',
            };
        }

        return [
            'category' => $category,
            'spent'    => $used,
            'limit'    => $limit,
            'percent'  => $percent,
            'status'   => $status,
        ];
    }
}
