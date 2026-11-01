<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = ['user_id', 'title', 'amount', 'category', 'spent_on'];

    protected function casts(): array
    {
        return [
            'amount'   => 'decimal:2',
            'spent_on' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
