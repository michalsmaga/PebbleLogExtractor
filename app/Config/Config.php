<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 22.11.18
 * Time: 15:23
 */

return [
    'paths' => [
        'inputPath' => '/home/michal/projects/sandbox/data',
        'date_csv' => '/home/michal/projects/sandbox/data/date_csv/csv_dates.csv',
        'logFile' => 'log.csv'
    ],
    'files' => [
        1=> [
            'remove_before_data' => true,
            'remove_between_data' => true,
            'remove_after_data' => true
        ],
        2 => [
            'remove_before_data' => true,
            'remove_between_data' => true,
            'remove_after_data' => true
        ],
        3 => [
            'remove_before_data' => true,
            'remove_between_data' => true,
            'remove_after_data' => true
        ]
    ],
    'output_file' => [
        'file_name' => 'pebble',
        'format' => 'csv'           // csv | json
    ]
];