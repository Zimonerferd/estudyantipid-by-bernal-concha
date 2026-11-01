<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsGoal extends Model
{
    protected $fillable = ['user_id', 'name', 'target_amount', 'saved_amount'];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'saved_amount'  => 'decimal:2',
        ];
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(SavingsDeposit::class);
    }

    /** Progress toward the target, 0 to 100 (two decimals). */
    public function percent(): float
    {
        $target = (float) $this->target_amount;

        return $target > 0
            ? min(100, round((float) $this->saved_amount / $target * 100, 2))
            : 0;
    }

    public function isComplete(): bool
    {
        return (float) $this->saved_amount >= (float) $this->target_amount;
    }
}
