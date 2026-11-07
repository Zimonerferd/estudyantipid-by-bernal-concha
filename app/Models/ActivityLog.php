<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['description'];

    /** One line in the admin "Recent activity" list. */
    public static function record(string $description): void
    {
        static::create(['description' => $description]);
    }
}
