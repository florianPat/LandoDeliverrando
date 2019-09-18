<?php

namespace MyVendor\Deliverrando\Domain\Validator;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class DelieveryTimeValidator extends AbstractValidator
{
    //NOTE: For the "options" property in the annotation
    protected $supportedOptions = [
        //Name       defaultValue, description, type, required
        'maximum' => [0, 'maximum value', 'integer', true],
    ];

    /**
     * @param mixed $value
     * @return void
     */
    protected function isValid($value) : void
    {
        $maximum = $this->getOptions()['maximum'];
        if(is_integer($value) && $value > 0 && $value <= $maximum) {
            return;
        }
        $this->addError('The number was not a value between 0 and ' . $maximum . '.', 5009832);
    }
}