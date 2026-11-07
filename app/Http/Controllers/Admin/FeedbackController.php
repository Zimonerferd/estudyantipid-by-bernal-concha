<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Message;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $types = config('estudyantipid.message_types');

        // Tab filter: ?type=report|suggestion|mail (none = All)
        $filter = $request->string('type')->toString();
        if (! isset($types[$filter])) {
            $filter = null;
        }

        $messages = Message::with('user')
            ->when($filter, fn ($query) => $query->where('type', $filter))
            ->latest()
            ->latest('id')
            ->take(100)
            ->get();

        // Open message: ?message=ID, or the newest one
        $selected = $messages->firstWhere('id', $request->integer('message')) ?? $messages->first();

        // Opening a message marks it as read
        if ($selected && ! $selected->read_at) {
            $selected->forceFill(['read_at' => now()])->save();
        }

        $unread = Message::whereNull('read_at')
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('admin.feedback', [
            'types'    => $types,
            'filter'   => $filter,
            'messages' => $messages,
            'selected' => $selected,
            'unread'   => $unread,
        ]);
    }

    public function reply(Request $request, Message $message)
    {
        $data = $request->validate([
            'reply' => ['required', 'string', 'max:3000'],
        ]);

        $message->forceFill([
            'admin_reply' => $data['reply'],
            'replied_at'  => now(),
            'read_at'     => $message->read_at ?? now(),
        ])->save();

        ActivityLog::record('Replied to ' . $message->user->name);

        return $this->backToMessage($request, $message);
    }

    /** Mark resolved, or reopen if it was already resolved. */
    public function resolve(Request $request, Message $message)
    {
        $resolving = $message->resolved_at === null;

        $message->forceFill(['resolved_at' => $resolving ? now() : null])->save();

        if ($resolving) {
            ActivityLog::record('Marked ' . $message->user->name . '’s ' . $message->type . ' as resolved');
        }

        return $this->backToMessage($request, $message);
    }

    private function backToMessage(Request $request, Message $message)
    {
        return redirect()->route('admin.feedback', array_filter([
            'type'    => $request->input('type'),
            'message' => $message->id,
        ]));
    }
}
