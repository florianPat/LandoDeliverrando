<?php

defined('TYPO3_MODE') || die('Access denied.');

// NOTE: the extension key, the name of the plugin (which is used in GET and POST)(in UpperCamelCase, but lowerCamelCase in GET/POST),
// which action from which controller can be executed,
// and which actions should not be cacheable
// NOTE: Also look into "tt_content"!

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
  'MyVendor.Deliverrando',
  'Productlist',
  [
      'StoreInventory' => 'index, add, remove, show, update, login, register, logout, endOrder, progressUpdate',
  ],
  [
      'StoreInventory' => 'index, add, remove, show, update, login, register, logout, endOrder, progressUpdate',
  ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'MyVendor.Deliverrando',
    'Bestellungen',
    [
        'Orders' => 'index, finish, ajax, updateProgress',
    ],
    [
        'Orders' => 'index, finish, ajax, updateProgress',
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\MyVendor\Deliverrando\Task\ProductQuantityCheckerTask::class] = [
    'extension' => $_EXTKEY,
    'title' => 'ProductQuantityChecker',
    'description' => 'Check if you need more!!',
    'additionalFields' => '',
];

$GLOBALS['TYPO3_CONF_VARS']['LOG']['Tx']['deliverrando']['writerConfiguration'] = [
    \TYPO3\CMS\Core\Log\LogLevel::DEBUG => [
        //NOTE: For a file
        'TYPO3\\CMS\\Core\\Log\\Writer\\FileWriter' => [
            'logFile' => 'typo3temp/logs/typo3_nice.log',
        ],
        //NOTE: For syslog (backend-module)
        //'TYPO3\\CMS\\Core\\Log\\Writer\\SyslogWriter' => [],
    ],
];
