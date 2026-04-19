<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'DeepL Base',
    'description' => 'Provides shared things across deepl related extensions, for example a shared point when overriding same TYPO3 backend fluid files are required and similar.',
    'category' => 'backend',
    'author' => 'web-vision GmbH Team',
    'author_company' => 'web-vision GmbH',
    'author_email' => 'hello@web-vision.de',
    'state' => 'stable',
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.5.99',
            'typo3' => '13.4.28-14.3.99',
            'backend' => '13.4.28-14.3.99',
            'extbase' => '13.4.28-14.3.99',
            'fluid' => '13.4.28-14.3.99',
            'setup' => '13.4.27-14.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
