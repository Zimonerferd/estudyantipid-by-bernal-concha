<?php

namespace App\Support;

use Carbon\Carbon;

class TimeAgo
{
    /**
     * "10 min ago", "1 hr ago", "3 hrs ago", "Yesterday", "Oct 9".
     * $compact = true gives "10m ago", "2h ago" (used on the admin Overview).
     */
    public static function format(Carbon $date, bool $compact = false): string
    {
        $minutes = (int) floor($date->diffInMinutes(now(), true));

        if ($minutes < 1) {
            return 'Just now';
        }

        if ($minutes < 60) {
            return $compact ? "{$minutes}m ago" : "{$minutes} min ago";
        }

        if ($date->isToday()) {
            $hours = intdiv($minutes, 60);

            return $compact ? "{$hours}h ago" : ($hours === 1 ? '1 hr ago' : "{$hours} hrs ago");
        }

        return $date->isYesterday() ? 'Yesterday' : $date->format('M j');
    }
}
