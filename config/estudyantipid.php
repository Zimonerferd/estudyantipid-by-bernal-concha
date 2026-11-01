<?php

return [
    // Options for the "College" dropdown on the Create Account page.
    // Only CCIS appears in the Figma design, so add the rest of your colleges here.
    'colleges' => [
        'CCIS',
    ],

    // Options for the category dropdown on the Expenses page.
    // Later the admin "Categories" screen will manage these from the database instead.
    'categories' => [
        'Food',
        'Transport',
        'School supplies',
    ],

    // Weekly spending limit per category (in pesos), shown on the Budgets page.
    // Same for every student for now. Editing limits per student comes later.
    'budgets' => [
        'Food'            => 1100,
        'Transport'       => 600,
        'School supplies' => 500,
    ],

    // Weekly amount a student aims to set aside (the "Savings" card on Budgets).
    'savings_budget' => 400,

    // The three tabs on the "Message us" page.
    'message_types' => [
        'report' => [
            'label'  => 'Report',
            'blurb'  => 'Something broken or wrong? Tell us what happened.',
            'button' => 'Send report',
        ],
        'suggestion' => [
            'label'  => 'Suggestion',
            'blurb'  => 'Have an idea to make Estudyantipid better?',
            'button' => 'Send suggestion',
        ],
        'mail' => [
            'label'  => 'Mail',
            'blurb'  => 'Anything else you want to ask the admin.',
            'button' => 'Send mail',
        ],
    ],
];
