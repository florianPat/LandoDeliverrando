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

        $errorResult = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Validation\Validator\NumberValidator::class)->validate($postcode);
        $errorResult->merge(GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Validation\Validator\StringLengthValidator::class,
            ['minimum' => 5, 'maximum' => 5])->validate($postcode));
        $errorResult->merge(GeneralUtility::makeInstance(\MyVendor\Deliverrando\Domain\Validator\PostCodeValidator::class)->validate($postcode));

        if($errorResult->hasErrors()) {
            foreach($errorResult->getErrors() as $error) {
                $this->addError("person.address:" . $error->getMessage(), $error->getCode());
            }
            return;
        }
    }
}