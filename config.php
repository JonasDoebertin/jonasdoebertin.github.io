<?php

return [
    'production' => false,
    'baseUrl' => 'http://localhost:8000/',
    'siteName' => 'Jonas Döbertin',
    'siteDescription' => 'Hi there! I’m Jonas Döbertin, a full stack web developer from Hamburg, Germany with a focus on Laravel and Statamic.',

    'collections' => [
        'notes' => [
            'path' => 'notes',
            'sort' => '-date',
        ],
        'projects' => [
            'sort' => 'priority',
        ],
    ],
];
