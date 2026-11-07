<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Expense;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * OPTIONAL demo data so the admin pages look filled in (the students from the Figma design).
 * Run it with:  php artisan db:seed --class=DemoSeeder
 * Every demo student's password is "password". Don't run this on a real deployment.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'mtconcha@snsu.edu.ph')->exists()) {
            $this->command?->info('Demo data is already there.');
            return;
        }

        $people = [
            'mtconcha'   => ['Mary Therese Concha', 'CCIS'],
            'rbernal'    => ['Rogen Bernal',        'CCIS'],
            'ldumalagan' => ['Lia Dumalagan',       'CAS'],
            'jpelayo'    => ['Joshua Pelayo',       'COE'],
            'avillaruz'  => ['Anika Villaruz',      'CBA'],
            'kalba'      => ['Kristine Alba',       'CBA'],
        ];

        $users = [];
        foreach ($people as $handle => [$name, $college]) {
            $users[$handle] = User::create([
                'name'     => $name,
                'email'    => $handle . '@snsu.edu.ph',
                'password' => 'password',
                'role'     => 'student',
                'college'  => $college,
            ]);
        }

        // Joshua is suspended, like in the design.
        $users['jpelayo']->forceFill(['suspended_at' => now()->subHours(2)])->save();

        // A few expenses per student on each day of this week up to today.
        mt_srand(42);
        $categories = ['Food', 'Transport', 'School supplies'];
        $titles = ['Lunch', 'Jeepney fare', 'Bond paper', 'Snacks', 'Printing', 'Milk tea'];

        foreach ($users as $user) {
            for ($i = 0; $i < 7; $i++) {
                $date = now()->startOfWeek()->addDays($i);

                if ($date->isFuture() && ! $date->isToday()) {
                    continue;
                }

                for ($n = mt_rand(1, 2); $n > 0; $n--) {
                    Expense::create([
                        'user_id'  => $user->id,
                        'title'    => $titles[array_rand($titles)],
                        'category' => $categories[array_rand($categories)],
                        'amount'   => mt_rand(20, 160),
                        'spent_on' => $date->toDateString(),
                    ]);
                }
            }
        }

        // The six messages from the Feedback screen.
        $messages = [
            ['ldumalagan', 'report',     'Expense total is wrong after deleting an entry',
                "Hi admin, I removed a ₱95 milk tea entry but my weekly total still shows the old\namount until I refresh the page. Can you check this?",
                now()->subMinutes(10), false, null],
            ['avillaruz',  'suggestion', 'Add a load & data category',
                'Most of my allowance goes to mobile data. Could you add a category for it?',
                now()->subHour(), false, null],
            ['rbernal',    'mail',       'Request to change registered email',
                'I would like to use my new school email instead. How do I change it?',
                now()->subHours(3), true, 'Hi Rogen, please send us your new email and we will update it for you.'],
            ['mtconcha',   'suggestion', 'Let us share savings goals with friends',
                'It would be fun to save toward a group goal with my classmates.',
                now()->subDay(), true, null],
            ['jpelayo',    'report',     'Cannot sign in on my phone',
                'The sign in button does nothing on my phone browser.',
                now()->subDays(5), true, 'Hi Joshua, this is fixed now. Please try again.'],
            ['kalba',      'mail',       'Question about the allowance reset day',
                'Can the allowance reset on a different day than Sunday?',
                now()->subDays(6), true, null],
        ];

        foreach ($messages as [$handle, $type, $subject, $body, $when, $read, $reply]) {
            Message::forceCreate([
                'user_id'     => $users[$handle]->id,
                'type'        => $type,
                'subject'     => $subject,
                'body'        => $body,
                'admin_reply' => $reply,
                'replied_at'  => $reply ? $when->copy()->addMinutes(30) : null,
                'read_at'     => $read ? $when->copy()->addMinutes(5) : null,
                'resolved_at' => $handle === 'jpelayo' ? $when->copy()->addHour() : null,
                'created_at'  => $when,
                'updated_at'  => $when,
            ]);
        }

        // Recent activity
        foreach ([
            ['Joshua Pelayo suspended', now()->subHours(2)],
            ['Replied to Rogen Bernal', now()->subHours(3)],
            ['6 new students onboarded', now()->subDay()],
        ] as [$text, $when]) {
            ActivityLog::forceCreate(['description' => $text, 'created_at' => $when, 'updated_at' => $when]);
        }
    }
}
