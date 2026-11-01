<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $types = config('estudyantipid.message_types');

        // Which tab is open: ?type=report|suggestion|mail (defaults to the first one)
        $type = $request->string('type')->toString();
        if (! isset($types[$type])) {
            $type = array_key_first($types);
        }

        return view('student.messages', [
            'types'    => $types,
            'type'     => $type,
            'messages' => Message::where('user_id', $request->user()->id)->latest()->take(20)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'    => ['required', Rule::in(array_keys(config('estudyantipid.message_types')))],
            'subject' => ['required', 'string', 'max:150'],
            'body'    => ['required', 'string', 'max:3000'],
        ], [], ['body' => 'message']);

        Message::create([...$data, 'user_id' => $request->user()->id]);

        return redirect()->route('student.messages', ['type' => $data['type']]);
    }
}
