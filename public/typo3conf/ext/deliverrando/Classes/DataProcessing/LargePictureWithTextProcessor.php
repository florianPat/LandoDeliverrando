<?php

namespace MyVendor\Deliverrando\DataProcessing;

use MyVendor\Deliverrando\Domain\Repository\SysTemplatesRepository;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ImageService;
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
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $images = $objectManager->get(FileRepository::class)->findByRelation('tt_content', 'image', $cObj->data['uid']);
        $header = $objectManager->get(SysTemplatesRepository::class)->findHeaderForLargePictureWithText($cObj->data['pid']);

        $processedData['backgroundImgUrl'] = $objectManager->get(ImageService::class)->getImageUri($images[0]);
        $processedData['header'] = $header;

        return $processedData;
    }
}