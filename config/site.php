<?php

return [
    'fallback_locale' => 'sk',

    'locales' => [
        'sk' => 'Slovenčina',
        'cs' => 'Čeština',
        'en' => 'English',
    ],

    'public_prefixes' => [
        'sk' => 'sk',
        'cs' => 'cz',
        'en' => 'en',
    ],

    'country_locale_map' => [
        'SK' => 'sk',
        'CZ' => 'cs',
    ],

    'paths' => [
        'sk' => [
            'about' => 'o-nas',
            'services' => 'sluzby',
            'projects' => 'projekty',
            'blog' => 'blog',
            'contact' => 'kontakt',
            'quote' => 'nezavazna-ponuka',
            'privacy' => 'ochrana-osobnych-udajov',
            'cookies' => 'cookies',
        ],
        'cs' => [
            'about' => 'o-nas',
            'services' => 'sluzby',
            'projects' => 'projekty',
            'blog' => 'blog',
            'contact' => 'kontakt',
            'quote' => 'nezavazna-nabidka',
            'privacy' => 'ochrana-osobnich-udaju',
            'cookies' => 'cookies',
        ],
        'en' => [
            'about' => 'about',
            'services' => 'services',
            'projects' => 'projects',
            'blog' => 'insights',
            'contact' => 'contact',
            'quote' => 'request-a-quote',
            'privacy' => 'privacy-policy',
            'cookies' => 'cookies',
        ],
    ],

    'contact' => [
        'company_name' => env('CENEOCORE_COMPANY_NAME', 'CENECORE'),
        'email' => env('CENEOCORE_CONTACT_EMAIL', 'hello@cenecore.com'),
        'phone' => env('CENEOCORE_CONTACT_PHONE', '+421 900 000 000'),
        'support_email' => env('CENEOCORE_SUPPORT_EMAIL', 'support@cenecore.com'),
        'quote_email' => env('CENEOCORE_QUOTE_EMAIL', 'quotes@cenecore.com'),
        'address' => env('CENEOCORE_ADDRESS', 'Bratislava, Slovensko'),
    ],
];
