<?php

namespace MyVendor\Deliverrando\TypoScriptUserFunc;

use MyVendor\Deliverrando\Domain\Repository\SysTemplatesRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DynamicPluginBound
{
    /**
     * @param string $content
     * @param array $conf
     * @return string
     */
    public function getSelectedPluginForTemplate(string $content, array $conf) : string
    {
        $sysTemplateRepository = GeneralUtility::makeInstance(SysTemplatesRepository::class);
        return $sysTemplateRepository->findPluginForTemplateForPid(intval($GLOBALS['TSFE']->id));
    }
}