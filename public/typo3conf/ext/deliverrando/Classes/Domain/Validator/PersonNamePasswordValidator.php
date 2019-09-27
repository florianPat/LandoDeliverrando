<?php

namespace MyVendor\Deliverrando\Domain\Validator;

use TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class PersonNamePasswordValidator extends AbstractValidator
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
        $person = $this->personRepository->findByName($value->getName());

        if($person !== null) {
            try {
                if (GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance("FE")->checkPassword($value->getPassword(), $person->getPassword())) {
                    return;
                } else {
                    $this->addError("person.password:This is the wrong password!", 1569568288);
                }
            } catch (InvalidPasswordHashException $e) {
                $this->addError('person.password:The password validation failed!', 1569569377);
            }
        } else {
            $this->addError("person.name:The name is not registered yet!", 1569568296);
        }
    }
}