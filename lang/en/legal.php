<?php

// Legal pages (privacy + terms). See lang/es/legal.php — Spanish is the source.
// NOT reviewed by a lawyer: written to describe accurately what this codebase
// actually does, so a review starts from something true.
return [
    'updated' => 'Last updated: 28 July 2026',

    'operator' => [
        'h' => 'Who runs this instance',
        'p' => 'This instance is run by :operator.',
        'contact' => 'For anything about your data you can write to :contact.',
    ],

    'privacy' => [
        'title' => 'Privacy',
        'intro' => 'Nexo Tools is the front door of the Nexo ecosystem: from here you discover the tools and go to the one you need. It is open source and self-hosted. We collect the minimum the hub needs, and nothing else. No tracking cookies, no third-party analytics, and nothing sent to advertising networks.',
        'sections' => [
            [
                'h' => 'Browsing the hub needs no account',
                'p' => 'The home page, the tool catalogue, the help centre and these very pages are public: you can read them without signing up and without us storing anything about you.',
            ],
            [
                'h' => 'What we store about your account',
                'p' => 'Only if you choose to create one: name, email and a hashed version of the password. The email is used to identify you when signing in and to send you the recovery link if you forget your password. We do not ask for a phone number, an address or payment details: the hub charges nothing.',
            ],
            [
                'h' => 'Your "your tools" panel',
                'p' => 'When you add a tool to your panel we store two things: your account and that tool\'s identifier in the catalogue (for example "nexoagenda"), with the date you added it. It is a list of shortcuts and nothing more: the hub does not ask the other tools for data and does not know what you do inside them, and only you can see your panel.',
            ],
            [
                'h' => 'Signing in with Nexo ID',
                'p' => 'It is optional. If you use it, Nexo ID hands us your account identifier, your name and your email, and we store that identifier so we can recognise you next time. You can also use the hub with a local account, without Nexo ID.',
            ],
            [
                'h' => 'Ecosystem metrics, without cookies',
                'p' => 'This hub counts visits to the Nexo tools and to alvarocdev.com. Each visit stores the tool that emitted it, the path visited without anything after the "?", the day, the country if the network reports it, and which tool you came from. Your IP and your browser are not stored: they are used on the fly to derive an anonymous fingerprint that includes the date, so today\'s fingerprint cannot be compared with tomorrow\'s or cross-referenced with another site\'s. No cookie is set to measure anything, and if your browser sends "Do Not Track" or "Global Privacy Control" nothing is stored at all. Whoever runs the instance sees totals per day, per tool and the most visited paths, never individual visits. On top of that, measurement is optional per installation and ships turned off.',
            ],
            [
                'h' => 'Cookies',
                'p' => 'Only the ones the site needs to work: the session cookie (to keep you signed in if you have an account) and the ones remembering your language and light/dark preference. The last two are shared with the other Nexo tools on the same domain, so your choice follows you from one to another. None are used for advertising or tracking.',
            ],
            [
                'h' => 'Email',
                'p' => 'The hub only sends the password recovery email. It is delivered through an external email provider, which necessarily processes the destination address and the message content in order to deliver it.',
            ],
            [
                'h' => 'How long',
                'p' => 'Your account and its panel are kept for as long as the account exists; deleting it also deletes the list of tools you added. Visit counts are not tied to any account and remain as figures per day and per tool.',
            ],
            [
                'h' => 'Your rights',
                'p' => 'You can request access to your data, its correction or its deletion by writing to whoever runs this instance (the contact is on the help page).',
            ],
            [
                'h' => 'Other instances',
                'p' => 'Nexo Tools can be installed on any server. Each installation is independent and responsible for its own data: this policy covers only this instance. Each tool in the catalogue also has its own policy.',
            ],
        ],
    ],

    'terms' => [
        'title' => 'Terms of use',
        'intro' => 'By using this Nexo Tools instance you accept the following. It is a free service, offered as is.',
        'sections' => [
            [
                'h' => 'What the service is',
                'p' => 'An entry point to the Nexo ecosystem: it shows you what each tool does and takes you there. With an account you can also build a panel with the ones you use. The hub does not provide each tool\'s service: every tool has its own operator, terms and privacy policy.',
            ],
            [
                'h' => 'Your account',
                'p' => 'The account is optional: it is only needed for the "your tools" panel. You can create it here or sign in with Nexo ID. You are responsible for what happens with your account and for keeping your password safe.',
            ],
            [
                'h' => 'The tools in the catalogue',
                'p' => 'The catalogue links point to ecosystem tools hosted separately. Listing them here does not make us responsible for how they work, whether they are available, or the use you make of them.',
            ],
            [
                'h' => 'Misuse',
                'p' => 'Automating sign-ups, overloading the service, or attempting to reach other people\'s accounts or data is not allowed. Whoever runs this instance may limit access or close an account doing any of that.',
            ],
            [
                'h' => 'Ecosystem metrics',
                'p' => 'If this instance has measurement enabled, it receives visit counts from the ecosystem tools. Only data from the tools listed in the catalogue is accepted, and it never includes personal data.',
            ],
            [
                'h' => 'Availability',
                'p' => 'The service is offered with no availability guarantee. We do what is reasonable to keep it online, but there may be interruptions. The hub being down does not stop you from reaching each tool at its own address.',
            ],
            [
                'h' => 'Limitation of liability',
                'p' => 'Whoever runs this instance is not liable for damages arising from the use of the service, including data loss.',
            ],
            [
                'h' => 'Free software',
                'p' => 'Nexo Tools is distributed under the MIT licence: you can read the code, modify it and host your own instance. The software is provided without warranties, as that licence states.',
            ],
            [
                'h' => 'Changes',
                'p' => 'These terms may change. The date above shows the last update.',
            ],
        ],
    ],
];
