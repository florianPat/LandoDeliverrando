<?php

namespace MyVendor\Deliverrando\Domain\Validator;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class PostCodeValidator extends AbstractValidator
{
    /**
     * @param mixed $value
     * @return void
     */
    protected function isValid($value) : void
    {
        $response = file_get_contents('http://dev.virtualearth.net/REST/v1/Locations?countryRegion=DE&postalCode=' . $value .
            '&key=YOUR_BING_API_KEY');
        $json = json_decode($response);
        assert($json->statusCode == 200);

        $ressourceSetLength = $json->resourceSets[0]->estimatedTotal;

        if($ressourceSetLength === 0 || (!isset($json->resourceSets[0]->resources[0]->address->postalCode)) || $json->resourceSets[0]->resources[0]->address->postalCode !== $value) {
            $this->addError("person.address:This is not a valid post code!", 23823892894839);
        }

        //TODO: I should set the locality! (But not in the Validator! (1. It is no validation, 2. I can not "persist" it here))
        //NOTE: But works even without the locality, so I do not care about it for now
        //$value->setAddress($value->getAddress() . ';' . $json->resourceSets[0]->resources[0]->address->locality);
    }
}