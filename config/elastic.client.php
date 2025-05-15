<?php declare(strict_types=1);

return [
    'default' => env('ELASTIC_CONNECTION', 'elasticsearch'),
    'connections' => [
        'elasticsearch' => [
            'hosts' => [
                env('ELASTIC_HOST', 'elasticsearch:9200'),
            ],
        ],
    ],
];
