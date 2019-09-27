<?php


namespace MyVendor\Deliverrando\Controller\Helper;

use MyVendor\Deliverrando\Domain\Model\Person;

class CustomerSessionHelper
{
    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Person $person
     * @param int $deliverrandoUid
     * @return void
     */
    public static function login(Person $person, int $deliverrandoUid) : void
    {
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'uid', $person->getUid());
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'dUid', intval($deliverrandoUid));
    }

    /**
     * @return void
     */
    public static function logout() : void
    {
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'uid', null);
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'dUid', null);
    }
}