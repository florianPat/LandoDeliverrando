<?php

namespace MyVendor\Deliverrando\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

class CategoryRepository extends Repository
{
    public function findAllForFormOptions() : array
    {
        $allCategories = $this->findAll();
        $result = [0 => ''];
        $allCategories->rewind();

        foreach($allCategories as $it) {
            $result[$it->getUid()] = $it->getName();
        }

        return $result;
    }
}