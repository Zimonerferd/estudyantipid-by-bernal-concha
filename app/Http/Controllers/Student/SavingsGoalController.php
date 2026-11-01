<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SavingsDeposit;
use App\Models\SavingsGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SavingsGoalController extends Controller
{
    /** How much the "+ Add ₱100" button puts in. */
    private const QUICK_ADD = 100;

    public function index(Request $request)
    {
        return $this->show($request, creating: false);
    }

    /** Same page with the "new goal" form open. */
    public function create(Request $request)
    {
        return $this->show($request, creating: true);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'target_amount' => ['required', 'numeric', 'min:1', 'max:10000000'],
            'saved_amount'  => ['nullable', 'numeric', 'min:0', 'lte:target_amount'],
        ], [], [
            'target_amount' => 'target',
            'saved_amount'  => '"saved so far"',
        ]);

        SavingsGoal::create([
            'user_id'       => $request->user()->id,
            'name'          => $data['name'],
            'target_amount' => $data['target_amount'],
            'saved_amount'  => $data['saved_amount'] ?? 0,
        ]);

        return redirect()->route('student.goals');
    }

    public function deposit(Request $request, SavingsGoal $goal)
    {
        abort_unless((int) $goal->user_id === (int) $request->user()->id, 404);

        $remaining = (float) $goal->target_amount - (float) $goal->saved_amount;

        if ($remaining > 0) {
            $amount = min(self::QUICK_ADD, $remaining); // never go past the target

            DB::transaction(function () use ($goal, $request, $amount) {
                $goal->increment('saved_amount', $amount);

                SavingsDeposit::create([
                    'savings_goal_id' => $goal->id,
                    'user_id'         => $request->user()->id,
                    'amount'          => $amount,
                    'deposited_on'    => now()->toDateString(),
                ]);
            });
        }

        return redirect()->route('student.goals');
    }

    private function show(Request $request, bool $creating)
    {
        return view('student.goals', [
            'goals'    => SavingsGoal::where('user_id', $request->user()->id)->orderBy('id')->get(),
            'creating' => $creating,
        ]);
    }
}
