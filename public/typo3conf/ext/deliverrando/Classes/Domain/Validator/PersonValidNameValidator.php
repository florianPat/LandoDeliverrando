<?php

namespace MyVendor\Deliverrando\Domain\Validator;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use TYPO3\CMS\Extbase\Validation\Validator\NumberValidator;
use TYPO3\CMS\Extbase\Validation\Validator\StringLengthValidator;

class PersonValidNameValidator extends AbstractValidator
{
    /**
     * @var \MyVendor\Deliverrando\Domain\Repository\PersonRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    private $personRepository;

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
    protected function isValid($value): void
    {
        if ($this->personRepository->findByName($value->getName()) !== null) {
            $this->addError("person.name:The name is already registered!", 2839283);
        }

        $apiKey = $this->configurationManager
            ->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'deliverrando')['bingApiKey'];

        $postcode = $value->getAddress();

        $this->result = GeneralUtility::makeInstance(NumberValidator::class)->validate($postcode);
        $this->result->merge(GeneralUtility::makeInstance(StringLengthValidator::class,
            ['minimum' => 5, 'maximum' => 5])->validate($postcode));
        $this->result->merge(GeneralUtility::makeInstance(PostCodeValidator::class, ['apiKey' => $apiKey])->validate($postcode));
    }
}