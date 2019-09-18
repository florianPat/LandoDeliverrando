<?php

namespace MyVendor\Deliverrando\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

class PersonRepository extends Repository
{
    /**
     * @param string $name
     * @return \MyVendor\Deliverrando\Domain\Model\Person
     */
    public function findByName(string $name)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_deliverrando_domain_model_person');

        $statement = $queryBuilder->select('uid')->from('tx_deliverrando_domain_model_person')->where(
            $queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($name))
        )->execute();

        $result = $this->findByUid($statement->fetch()['uid']);

        return $result;
    }
}