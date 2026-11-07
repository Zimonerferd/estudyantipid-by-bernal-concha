<?php

return [
    // Options for the "College" dropdown on the Create Account page.
    // These are the colleges that appear in the Figma design, so add the rest of yours here.
    'colleges' => [
        'CCIS',
        'CAS',
        'COE',
        'CBA',
    ],

    // Weekly limit (in pesos) given to a category an admin adds on the Categories page.
    // Categories themselves now live in the database (admin > Categories).
    'default_category_limit' => 300,

    // The three tabs on the "Message us" page (and the filters on admin Feedback).
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
