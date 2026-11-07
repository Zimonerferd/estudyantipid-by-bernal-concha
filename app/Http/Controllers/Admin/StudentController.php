<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->string('q')->toString());

        $students = User::where('role', 'student')
            ->when($search !== '', fn ($query) => $query->where(
                fn ($w) => $w->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")
            ))
            ->orderBy('name')
            ->simplePaginate(15)
            ->withQueryString();

        // What each student on this page has spent this month
        $spent = DB::table('expenses')
            ->whereIn('user_id', $students->pluck('id'))
            ->whereDate('spent_on', '>=', now()->startOfMonth()->toDateString())
            ->whereDate('spent_on', '<=', now()->endOfMonth()->toDateString())
            ->selectRaw('user_id, SUM(amount) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        return view('admin.students', [
            'students' => $students,
            'spent'    => $spent,
            'search'   => $search,
        ]);
    }

    /** Suspend an active student, or reinstate a suspended one. */
    public function toggle(User $student)
    {
        abort_unless($student->role === 'student', 404);

        $suspending = $student->suspended_at === null;

        $student->forceFill(['suspended_at' => $suspending ? now() : null])->save();

        ActivityLog::record($student->name . ($suspending ? ' suspended' : ' reinstated'));

        return back();
    }
}
