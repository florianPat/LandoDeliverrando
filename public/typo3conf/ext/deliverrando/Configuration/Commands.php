<?php

use MyVendor\Deliverrando\Command\ProductOrderCommand;

return [
    'site:products:order' => [
        'class' => ProductOrderCommand::class
    ],
];