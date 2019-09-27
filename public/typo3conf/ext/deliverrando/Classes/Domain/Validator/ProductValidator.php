<?php

namespace MyVendor\Deliverrando\Domain\Validator;

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

//NOTE: This class validates the model "Product" as a whole!
class ProductValidator extends AbstractValidator
{
    /**
     * @param mixed $value
     * @return void
     */
    protected function isValid($value) : void
    {
        if($value->getName() === null || is_string($value->getName())) {
            return;
        } else {
            $this->addError('product.name:The name has to be a string!', 1569568342);
        }
    }
}