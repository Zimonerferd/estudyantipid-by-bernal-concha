<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    // admin_reply / replied_at / read_at / resolved_at are set by the admin side with forceFill().
    protected $fillable = ['user_id', 'type', 'subject', 'body'];

    protected function casts(): array
    {
        return [
            'replied_at'  => 'datetime',
            'read_at'     => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
