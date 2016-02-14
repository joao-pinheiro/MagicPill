<?php

return [
    'log' => [
        'stream' => [
            'writerName' => "Stream",
            'writerParams' => [
                'stream' => APPLICATION_PATH . "/data/log/application.log",
                'mode' => 'a'
            ],
            'filterName' => "Priority",
            'filterParams' => [
                'priority' => 6
            ]
        ]
    ],

];