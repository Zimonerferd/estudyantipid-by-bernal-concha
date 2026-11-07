<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'weekly_limit', 'is_savings'];

    protected function casts(): array
    {
        return [
            'weekly_limit' => 'decimal:2',
            'is_savings'   => 'boolean',
        ];
    }

    /** Categories students can log expenses against (everything except Savings). */
    public function scopeSpending(Builder $query): Builder
    {
        return $query->where('is_savings', false);
    }
}
