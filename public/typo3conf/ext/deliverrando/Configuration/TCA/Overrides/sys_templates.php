<?php

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

defined('TYPO3_MODE') || die();

// NOTE: Dadurch kann man das Template im Backend auswählen

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
 'deliverrando',
 'Configuration/TypoScript/Default',
 'Deliverrando'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'deliverrando',
    'Configuration/TypoScript/Json',
    'Json'
);

//NOTE: Adds a "select plugin dialog" in the select template dialog
$sysTemplateRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\MyVendor\Deliverrando\Domain\Repository\SysTemplatesRepository::class);
$staticTemplate = $sysTemplateRepository->findIncludeStaticFileByPageUid(7);
$findResult = strpos($staticTemplate, 'EXT:deliverrando/Configuration/TypoScript/Json');
if($findResult !== false) {
    $items = $sysTemplateRepository->findAllTtContentListTypes();

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_template', [
        'plugin_for_template' => [
            'label' => 'Plugin for Template',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => $items,
            ],
        ]
    ]);

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_template', 'plugin_for_template',
        '', 'after:include_static_file');
}