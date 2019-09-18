<?php

namespace MyVendor\Deliverrando\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Database\ConnectionPool;

class SysTemplatesRepository
{
    /**
     * @param int $pid
     * @return string
     */
    public function findIncludeStaticFileByPageUid(int $pid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_template');

        $statement = $queryBuilder->select('include_static_file')->from('sys_template')->where(
            $queryBuilder->expr()->eq('pid', $pid)
        )->execute();

        $result = $statement->fetch()['include_static_file'];

        return $result;
    }

    /**
     * @param int $pid
     * @return string
     */
    public function findPluginForTemplateForPid(int $pid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_template');

        $statement = $queryBuilder->select('plugin_for_template')->from('sys_template')->where(
            $queryBuilder->expr()->eq('pid', $pid)
        )->execute();

        $result = $statement->fetch()['plugin_for_template'];

        return $result;
    }

    /**
     * @return array
     */
    public function findAllTtContentListTypes()
    {
        //TODO: Fix that only used plugins are returned!!
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');

        $statement = $queryBuilder->select('list_type')->from('tt_content')->execute();

        $result = [];
        while($row = $statement->fetch()) {
            $listType = $row['list_type'];
            if($listType !== '') {
                $duplicate = false;
                foreach($result as $item) {
                    if($item[0] == $listType) {
                        $duplicate = true;
                        break;
                    }
                }
                if(!$duplicate) {
                    array_push($result, [$listType, $listType]);
                }
            }
        }
        return $result;
    }

    /**
     * @param int $pid
     */
    public function findHeaderForLargePictureWithText(int $pid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');

        $statement = $queryBuilder->select('header')->from('tt_content')->where(
            $queryBuilder->expr()->eq('pid', $pid),
            $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('deliverrando_largeimagetext'))
        )->execute();

        return $statement->fetch()['header'];
    }
}