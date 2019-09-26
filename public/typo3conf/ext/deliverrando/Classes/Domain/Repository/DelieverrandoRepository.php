<?php

namespace MyVendor\Deliverrando\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Database\ConnectionPool;

class DelieverrandoRepository extends Repository
{
    /**
     * @param int $userGroupUid
     * @return string
     */
    public function findDelieverRandoUid(int $userGroupUid) : string
    {
        //NOTE: I do not know why I do not use this!
        //return $this->findOneByUserGroup($userGroupUid)->getUid();

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_deliverrando_domain_model_delieverrando');

        $statement = $queryBuilder->select('uid')->from('tx_deliverrando_domain_model_delieverrando')->where(
            $queryBuilder->expr()->eq('user_group', $queryBuilder->createNamedParameter($userGroupUid))
        )->execute();

        return $statement->fetch()['uid'];
    }

    /**
     * @param int $userGroupUid
     * @return int
     */
    private function findSubGroup(int $userGroupUid) : int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('fe_groups');

        $statement = $queryBuilder->select('subgroup')->from('fe_groups')->where(
            $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($userGroupUid))
        )->execute();

        $result = $statement->fetch()['subgroup'];

        if($result !== '') {
            return $result;
        }  else {
            return -1;
        }
    }

    /**
     * @param int $userGroupUid
     * @return array
     */
    public function findDelieverRandoUidsForUserGroup(int $userGroupUid) : array
    {
        $delieverrandoSubGroupIds = [];
        $result = [$this->findDelieverRandoUid($userGroupUid)];

        for($subGroup = $this->findSubGroup($userGroupUid);
            $subGroup != -1;
            $subGroup = $this->findSubGroup($subGroup)) {
            array_push($delieverrandoSubGroupIds, $subGroup);
        }
        foreach($delieverrandoSubGroupIds as $it) {
            array_push($result, $this->findDelieverRandoUid($it));
        }

        return $result;
    }
}