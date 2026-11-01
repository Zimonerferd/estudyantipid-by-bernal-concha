<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\WeeklySummary;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('student.budgets', [
            'budgets' => (new WeeklySummary($request->user()))->budgets(),
        ]);
    }
}
