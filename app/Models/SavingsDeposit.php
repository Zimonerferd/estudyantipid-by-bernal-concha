<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingsDeposit extends Model
{
    protected $fillable = ['savings_goal_id', 'user_id', 'amount', 'deposited_on'];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'deposited_on' => 'date',
        ];
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(SavingsGoal::class, 'savings_goal_id');
    }
}
