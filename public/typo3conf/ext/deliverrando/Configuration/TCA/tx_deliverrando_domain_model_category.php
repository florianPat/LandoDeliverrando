<?php

// NOTE: How a new instance can be added to the database through the backend
return [
    'ctrl' => [
        'title' => 'Category',
        'label' => 'name',
    ],
    'columns' => [
        'name' => [
            'label' => 'Name',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'eval' => 'trim,required',
                'max' => '30',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'name'],
    ],
];