<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Expense;
use App\Models\SavingsDeposit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * One place that works out "this week" (Monday to Sunday) for a student,
 * so the Dashboard, Budgets and Analytics pages always agree.
 *
 * Date ranges use whereDate() on purpose: it works whether the database stored
 * "2026-11-08" or "2026-11-08 00:00:00", so Sunday is never left out.
 */
class WeeklySummary
{
    /** At or above this percent a spending budget shows "Near limit". */
    public const NEAR_LIMIT = 85;

    private Collection $expenses;

    public function __construct(private User $user)
    {
        $this->expenses = Expense::where('user_id', $user->id)
            ->whereDate('spent_on', '>=', $this->weekStart()->toDateString())
            ->whereDate('spent_on', '<=', $this->weekEnd()->toDateString())
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
            ->whereDate('spent_on', '>=', now()->subWeek()->startOfWeek()->toDateString())
            ->whereDate('spent_on', '<=', now()->subWeek()->endOfWeek()->toDateString())
            ->sum('amount');
    }

    /** Pesos put into savings goals this week. */
    public function savedThisWeek(): float
    {
        return (float) SavingsDeposit::where('user_id', $this->user->id)
            ->whereDate('deposited_on', '>=', $this->weekStart()->toDateString())
            ->whereDate('deposited_on', '<=', $this->weekEnd()->toDateString())
            ->sum('amount');
    }

    /** Weekly allowance = every category's weekly limit added together (spending + savings). */
    public function allowance(): float
    {
        return (float) Category::sum('weekly_limit');
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
     * One budget card per category (set on the admin Categories page).
     * status: 'ok' | 'near' | 'over' (Savings is always 'ok': filling it up is good).
     */
    public function budgets(): array
    {
        $spent = $this->spentByCategory();
        $saved = $this->savedThisWeek();
        $cards = [];

        foreach (Category::orderBy('id')->get() as $category) {
            $isSpending = ! $category->is_savings;

            $cards[] = $this->card(
                $category->name,
                $isSpending ? (float) $spent->get($category->name, 0) : $saved,
                (float) $category->weekly_limit,
                $isSpending
            );
        }

        return $cards;
    }

    private function card(string $category, float $used, float $limit, bool $isSpending): array
    {
        $percent = $limit > 0 ? (int) round($used / $limit * 100) : 0;

        $status = 'ok';
        if ($isSpending) {
            $status = match (true) {
                $percent > 100               => 'over',
                $percent >= self::NEAR_LIMIT => 'near',
                default                      => 'ok',
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
