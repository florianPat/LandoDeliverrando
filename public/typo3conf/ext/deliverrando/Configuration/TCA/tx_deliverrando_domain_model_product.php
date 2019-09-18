<?php

// So sieht der Table im Backend aus!
return [
  'ctrl' => [
    'title' => 'Product',
    'label' => 'name',
  ],
  'columns' => [
    'name' => [
      'label' => 'Name',
      'config' => [
          'type' => 'input',
          'size' => '20',
          'eval' => 'trim,required',
          'max' => 30,
      ],
    ],
    'description' => [
      'label' => 'Description',
      'config' => [
        'type' => 'text',
        'eval' => 'trim',
        'max' => '100',
      ],
    ],
    'quantity' => [
      'label' => 'Quantity',
      'config' => [
        'type' => 'input',
        'size' => '4',
        'eval' => 'int',
        'range' => [
            'lower' => 0,
            'upper' => 1000,
        ],
      ],
    ],
    'deliverytime' => [
      'label' => 'Delievery time',
      'config' => [
          'type' => 'input',
          'size' => '3',
          'eval' => 'int',
          'range' => [
              'lower' => 0,
              'upper' => 100,
          ],
      ],
    ],
    'categories' => [
        'label' => 'Categories',
        'config' => [
            'type' => 'select',
            'size' => '5',
            'minitems' => '0',
            'maxitems' => '5',
            'multiple' => '0',
            'autoSizeMax' => '5',
            // NOTE: Which table to choose from, and where to store it (foreign_field is used if it is a 1:n relation.
            // One would habe to set the field of the "child" table
            'foreign_table' => 'tx_deliverrando_domain_model_category',
            'MM' => 'tx_deliverrando_product_category_mm',
        ],
    ],
  ],
  'types' => [
    '0' => ['showitem' => 'name, description, quantity, deliverytime, categories'],
  ],
];
