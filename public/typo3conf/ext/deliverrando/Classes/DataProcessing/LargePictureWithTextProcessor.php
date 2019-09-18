<?php

namespace MyVendor\Deliverrando\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class LargePictureWithTextProcessor implements DataProcessorInterface
{
    /**
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        $fileRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
        $object = $fileRepository->findByRelation('tt_content', 'image', 1);
        $url = $object[0]->getPublicUrl();

        $objectManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
        $sysTemplateRepository = $objectManager->get(\MyVendor\Deliverrando\Domain\Repository\SysTemplatesRepository::class);
        $header = $sysTemplateRepository->findHeaderForLargePictureWithText(1);

        $processedData['backgroundImgUrl'] = '../../../../../../../' . $url;
        $processedData['header'] = $header;

        return $processedData;
    }
}