<?php

namespace MyVendor\Deliverrando\TypoScriptUserFunc;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\CaseContentObject;

class DynamicPluginBound
{
    /**
     * @param string $content
     * @param array $conf
     * @return string
     */
    public function getSelectedPluginForTemplate(string $content, array $conf) : string
    {
        $sysTemplateRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\MyVendor\Deliverrando\Domain\Repository\SysTemplatesRepository::class);
        return $sysTemplateRepository->findPluginForTemplateForPid(intval($GLOBALS['TSFE']->id));
    }
}