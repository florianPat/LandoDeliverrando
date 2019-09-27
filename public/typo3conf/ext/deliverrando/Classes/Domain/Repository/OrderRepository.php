<?php

namespace MyVendor\Deliverrando\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Repository;

class OrderRepository extends Repository
{
    /**
     * @return array
     */
    public function transferAllToJsonArray() : array
    {
        $ordersNativeArray = $this->findAll()->toArray();
        $ordersNativeArrayLength = count($ordersNativeArray);
        $result = [];
        for($i = 0; $i < $ordersNativeArrayLength; ++$i) {
            $it = $ordersNativeArray[$i];
            $person = $it->getPerson();
            $productDescriptions = [];
            $finishLink = GeneralUtility::makeInstance(ObjectManager::class)->get(UriBuilder::class)->setTargetPageUid(5)->setArguments(['tx_deliverrando_bestellungen[action]' => 'finish', 'tx_deliverrando_bestellungen[controller]' => 'Orders', 'tx_deliverrando_bestellungen[order]' => $it->getUid()])->buildFrontendUri();;
            foreach($it->getProductDescriptions() as $productDesc) {
                array_push($productDescriptions, [
                    'productUid' => $productDesc->getProduct()->getUid(),
                    'productName' => $productDesc->getProduct()->getName(),
                    'quantity' => $productDesc->getQuantity(),
                ]);
            }

            array_push($result, [
                'uid' => $it->getUid(),
                'person' => [
                    'name' => $person->getName(),
                    'address' => $person->getAddress(),
                    'telephonenumber' => $person->getTelephonenumber(),
                ],
                'productDescriptions' => $productDescriptions,
                'finishLink' => $finishLink,
            ]);
        }

        return $result;
    }
}