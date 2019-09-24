<?php

namespace MyVendor\Deliverrando\Domain\Validator;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class PersonValidNameValidator extends AbstractValidator
{
    /**
     * @var \MyVendor\Deliverrando\Domain\Repository\PersonRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    private $personRepository;

    /**
     * @param mixed $value
     * @return void
     */
    protected function isValid($value) : void
    {
        if($this->personRepository->findByName($value->getName()) !== null) {
            $this->addError("person.name:The name is already registered!", 2839283);
        }

        $postcode = $value->getAddress();

        $errorResult =  GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Validation\Validator\NumberValidator::class)->validate($postcode);
        $errorResult->merge(GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Validation\Validator\StringLengthValidator::class,
            ['minimum' => 5, 'maximum' => 5])->validate($postcode));
        if($errorResult->hasErrors()) {
            foreach($errorResult->getErrors() as $error) {
                $this->addError("person.address:" . $error->getMessage(), $error->getCode());
            }
            return;
        }

        $response = file_get_contents('http://dev.virtualearth.net/REST/v1/Locations?countryRegion=DE&postalCode=' . $postcode .
            '&key=YOUR_BING_API_KEY');
        $json = json_decode($response);
        assert($json->statusCode == 200);

        $ressourceSetLength = $json->resourceSets[0]->estimatedTotal;

        if($ressourceSetLength === 0 || (!isset($json->resourceSets[0]->resources[0]->address->postalCode)) || $json->resourceSets[0]->resources[0]->address->postalCode !== $postcode) {
            $this->addError("person.address:This is not a valid post code!", 23823892894839);
        } else {
            $value->setAddress($value->getAddress() . ';' . $json->resourceSets[0]->resources[0]->address->locality);
        }
    }
}