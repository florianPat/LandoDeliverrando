<?php

namespace MyVendor\Deliverrando\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 *  @package MyVendor\Deliverrando\Domain\Repository
 */
class ProductRepository extends Repository
{
    /**
     * @param array $uids
     * @return array
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAllWithDieverRandoUids(array $uids) : array
    {
        $query = $this->createQuery();
        $query->matching($query->in('delieverrando', $uids));
        return $query->execute()->toArray();
    }
}
