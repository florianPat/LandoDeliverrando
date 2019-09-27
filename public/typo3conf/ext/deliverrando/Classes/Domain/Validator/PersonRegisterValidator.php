<?php

namespace MyVendor\Deliverrando\Domain\Validator;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use TYPO3\CMS\Extbase\Validation\Validator\NumberValidator;
use TYPO3\CMS\Extbase\Validation\Validator\StringLengthValidator;

class PersonRegisterValidator extends AbstractValidator
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
    protected function isValid($value): void
    {
        if ($this->personRepository->findByName($value->getName()) !== null) {
            $this->addError("person.name:The name is already registered!", 1569568323);
        }

        $postcode = $value->getAddress();

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->result = $objectManager->get(NumberValidator::class)->validate($postcode);
        $this->result->merge($objectManager->get(StringLengthValidator::class,
            ['minimum' => 5, 'maximum' => 5])->validate($postcode));
        $this->result->merge($objectManager->get(PostCodeValidator::class)->validate($postcode));
    }
}