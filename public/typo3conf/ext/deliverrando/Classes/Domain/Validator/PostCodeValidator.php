<?php

namespace MyVendor\Deliverrando\Domain\Validator;

use MyVendor\Deliverrando\Controller\Helper\BingMapsRestApiHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class PostCodeValidator extends AbstractValidator
{
    protected $supportedOptions = [
      'apiKey' => ['', 'Bing API Key', 'string'],
    ];

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param mixed $value
     * @return void
     */
    protected function isValid($value) : void
    {
        $apiKey = $this->options['apiKey'];
        if($apiKey === '') {
            $apiKey = $this->configurationManager
                ->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'deliverrando')['bingApiKey'];
        }

        $bingMapsRestApiHelper = GeneralUtility::makeInstance(ObjectManager::class)->get(BingMapsRestApiHelper::class);

        $json = $bingMapsRestApiHelper->makeApiCall('/Locations?countryRegion=DE&postalCode=' . $value, $apiKey);
        if($json === 'InvalidStatusCode') {
            $this->addError('person.address:There was an error on the server :/', 1569568236);
            return;
        }

        $ressourceSetLength = $json->resourceSets[0]->estimatedTotal;

        if($ressourceSetLength === 0 || (!isset($json->resourceSets[0]->resources[0]->address->postalCode)) || $json->resourceSets[0]->resources[0]->address->postalCode !== $value) {
            $this->addError("person.address:This is not a valid post code!", 1569568268);
        }
    }
}