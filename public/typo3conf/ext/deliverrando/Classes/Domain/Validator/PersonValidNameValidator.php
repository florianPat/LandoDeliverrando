<?php

namespace MyVendor\Deliverrando\Domain\Validator;

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
        if($this->personRepository->findByName($value->getName()) === null) {
            return;
        } else {
            $this->addError("person.name:The name is already registered!", 2839283);
        }
    }
}