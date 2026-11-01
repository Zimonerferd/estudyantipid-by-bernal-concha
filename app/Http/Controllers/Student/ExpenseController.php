<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        // This week (Monday to Sunday), same week the Dashboard talks about.
        $expenses = Expense::where('user_id', $request->user()->id)
            ->whereBetween('spent_on', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            ])
            ->orderByDesc('spent_on')
            ->orderByDesc('id')
            ->get();

        return view('student.expenses', [
            'expenses'   => $expenses,
            'total'      => $expenses->sum('amount'),
            'categories' => config('estudyantipid.categories'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => ['required', 'string', 'max:100'],
            'amount'   => ['required', 'numeric', 'min:0.01', 'max:1000000'],
            'category' => ['required', Rule::in(config('estudyantipid.categories'))],
        ]);

        Expense::create([
            ...$data,
            'user_id'  => $request->user()->id,
            'spent_on' => now()->toDateString(),
        ]);

        return redirect()->route('student.expenses');
    }
}
