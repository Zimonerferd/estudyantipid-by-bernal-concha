<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    // admin_reply / replied_at are filled in by the admin side later.
    protected $fillable = ['user_id', 'type', 'subject', 'body'];

    protected function casts(): array
    {
        return ['replied_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
