<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        // This week (Monday to Sunday), same week the Dashboard talks about.
        $expenses = Expense::where('user_id', $request->user()->id)
            ->whereDate('spent_on', '>=', now()->startOfWeek()->toDateString())
            ->whereDate('spent_on', '<=', now()->endOfWeek()->toDateString())
            ->orderByDesc('spent_on')
            ->orderByDesc('id')
            ->get();

        return view('student.expenses', [
            'expenses'   => $expenses,
            'total'      => $expenses->sum('amount'),
            'categories' => $this->categoryNames(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => ['required', 'string', 'max:100'],
            'amount'   => ['required', 'numeric', 'min:0.01', 'max:1000000'],
            'category' => ['required', Rule::in($this->categoryNames()->all())],
        ]);

        Expense::create([
            ...$data,
            'user_id'  => $request->user()->id,
            'spent_on' => now()->toDateString(),
        ]);

        return redirect()->route('student.expenses');
    }

    /** Categories an admin has set up (everything except Savings). */
    private function categoryNames()
    {
        return Category::spending()->orderBy('id')->pluck('name');
    }
}
