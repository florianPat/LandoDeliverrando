<?php

// NOTE: The extension key, the name of the plugin (the one from setupPlugin in ext_localconf.php),
// a label for the plugin

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
  'MyVendor.Deliverrando',
  'Productlist',
  'Die Produktliste + Bestellungen'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'MyVendor.Deliverrando',
    'Bestellungen',
    'Die Bestellungen der Kunden'
);

//NOTE: Add the content element to the list
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    ['LargePictureWithText', 'deliverrando_largeimagetext'],
    'CType',
    'deliverrando'
);

//NOTE: Define the tca for the new content element
$GLOBALS['TCA']['tt_content']['types']['deliverrando_largeimagetext'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            header;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_formlabel,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.images,
            image,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
            rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
   ',
];