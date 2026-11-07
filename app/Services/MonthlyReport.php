<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\SavingsDeposit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/** Everything on the student's Monthly reports page, for one calendar month. */
class MonthlyReport
{
    private Collection $expenses;

    public function __construct(private User $user, private Carbon $month)
    {
        $this->expenses = Expense::where('user_id', $user->id)
            ->whereDate('spent_on', '>=', $month->copy()->startOfMonth()->toDateString())
            ->whereDate('spent_on', '<=', $month->copy()->endOfMonth()->toDateString())
            ->orderByDesc('spent_on')
            ->orderByDesc('id')
            ->get();
    }

    /** The month's expenses, newest first. */
    public function expenses(): Collection
    {
        return $this->expenses;
    }

    public function total(): float
    {
        return (float) $this->expenses->sum('amount');
    }

    public function count(): int
    {
        return $this->expenses->count();
    }

    /** Days the average is spread over: today's date for this month, the whole month for past ones. */
    public function daysCounted(): int
    {
        return $this->month->isSameMonth(now()) ? now()->day : $this->month->daysInMonth;
    }

    public function dailyAverage(): float
    {
        $days = $this->daysCounted();

        return $days > 0 ? $this->total() / $days : 0.0;
    }

    /** @return array<int, array{name: string, amount: float, percent: int}> biggest first */
    public function byCategory(): array
    {
        $total = $this->total();

        return $this->expenses
            ->groupBy('category')
            ->map(fn ($group, $name) => [
                'name'    => $name,
                'amount'  => (float) $group->sum('amount'),
                'percent' => $total > 0 ? (int) round($group->sum('amount') / $total * 100) : 0,
            ])
            ->sortByDesc('amount')
            ->values()
            ->all();
    }

    /** Spending in blocks of 7 days: days 1-7, 8-14, 15-21, 22-28, and the rest. */
    public function byWeek(): array
    {
        $daysInMonth = $this->month->daysInMonth;
        $weeks = (int) ceil($daysInMonth / 7);
        $amounts = array_fill(1, $weeks, 0.0);

        foreach ($this->expenses as $expense) {
            $week = min($weeks, (int) ceil($expense->spent_on->day / 7));
            $amounts[$week] += (float) $expense->amount;
        }

        $result = [];
        foreach ($amounts as $week => $amount) {
            $from = ($week - 1) * 7 + 1;
            $to = $week === $weeks ? $daysInMonth : $week * 7;

            $result[] = ['label' => "Wk {$week}", 'range' => "{$from}–{$to}", 'amount' => $amount];
        }

        return $result;
    }

    public function topExpenses(int $limit = 5): Collection
    {
        return $this->expenses->sortByDesc('amount')->take($limit)->values();
    }

    /** Pesos put into savings goals during the month. */
    public function saved(): float
    {
        return (float) SavingsDeposit::where('user_id', $this->user->id)
            ->whereDate('deposited_on', '>=', $this->month->copy()->startOfMonth()->toDateString())
            ->whereDate('deposited_on', '<=', $this->month->copy()->endOfMonth()->toDateString())
            ->sum('amount');
    }

    public function previousTotal(): float
    {
        $previous = $this->month->copy()->subMonthNoOverflow();

        return (float) Expense::where('user_id', $this->user->id)
            ->whereDate('spent_on', '>=', $previous->copy()->startOfMonth()->toDateString())
            ->whereDate('spent_on', '<=', $previous->copy()->endOfMonth()->toDateString())
            ->sum('amount');
    }
}
