<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Message;
use App\Models\SavingsDeposit;
use App\Models\SavingsGoal;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * DEV ONLY. Admins can't self-register, so one is seeded here.
     * Change these credentials (or remove this) before deploying anywhere real.
     * The User model's "hashed" password cast hashes the plain text for us.
     * Safe to run more than once: each demo block only runs if the student has none yet.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@snsu.edu.ph'],
            ['name' => 'Admin', 'password' => 'password', 'role' => 'admin']
        );

        $student = User::updateOrCreate(
            ['email' => 'student@snsu.edu.ph'],
            ['name' => 'Juan Dela Cruz', 'password' => 'password', 'role' => 'student', 'college' => 'CCIS']
        );

        // Demo expenses, dated inside the current week.
        if (! Expense::where('user_id', $student->id)->exists()) {
            $day = fn (int $ago) => now()->subDays($ago)->max(now()->startOfWeek())->toDateString();

            foreach ([
                ['Lunch at canteen',    'Food',            85,  $day(0)],
                ['Tricycle to campus',  'Transport',       30,  $day(0)],
                ['Bond paper (1 ream)', 'School supplies', 240, $day(1)],
                ['Milk tea',            'Food',            95,  $day(1)],
                ['Jeepney fare',        'Transport',       24,  $day(2)],
            ] as [$title, $category, $amount, $date]) {
                Expense::create([
                    'user_id'  => $student->id,
                    'title'    => $title,
                    'category' => $category,
                    'amount'   => $amount,
                    'spent_on' => $date,
                ]);
            }
        }

        // Demo savings goals (the ones from the Figma design) + one deposit this week.
        if (! SavingsGoal::where('user_id', $student->id)->exists()) {
            foreach ([
                ['New earphones',         2600, 1400],
                ['Thesis printing fund',  1500, 800],
                ['Foundation week outfit', 900, 300],
            ] as $i => [$name, $target, $saved]) {
                $goal = SavingsGoal::create([
                    'user_id'       => $student->id,
                    'name'          => $name,
                    'target_amount' => $target,
                    'saved_amount'  => $saved,
                ]);

                if ($i === 0) {
                    SavingsDeposit::create([
                        'savings_goal_id' => $goal->id,
                        'user_id'         => $student->id,
                        'amount'          => 200,
                        'deposited_on'    => now()->toDateString(),
                    ]);
                }
            }
        }

        // Demo messages: one waiting for a reply, one already answered.
        if (! Message::where('user_id', $student->id)->exists()) {
            Message::forceCreate([
                'user_id'    => $student->id,
                'type'       => 'suggestion',
                'subject'    => 'Let us share savings goals with friends',
                'body'       => 'It would be fun to save toward a group goal with my classmates.',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ]);

            Message::forceCreate([
                'user_id'     => $student->id,
                'type'        => 'mail',
                'subject'     => 'Question about my allowance reset day',
                'body'        => 'Can the allowance reset on a different day than Sunday?',
                'admin_reply' => 'Hi Juan, the reset day is fixed to Sunday for now, but we will note your request.',
                'replied_at'  => now()->subDays(2),
                'created_at'  => now()->subDays(3),
                'updated_at'  => now()->subDays(2),
            ]);
        }
    }
}
