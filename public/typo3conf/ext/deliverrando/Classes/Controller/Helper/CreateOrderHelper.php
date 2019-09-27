<?php


namespace MyVendor\Deliverrando\Controller\Helper;


use MyVendor\Deliverrando\Domain\Model\Person;
use MyVendor\Deliverrando\Domain\Repository\OrderRepository;
use MyVendor\Deliverrando\Domain\Repository\PersonRepository;
use MyVendor\Deliverrando\Domain\Repository\ProductRepository;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class CreateOrderHelper
{
    /**
     * @var \MyVendor\Deliverrando\Domain\Repository\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \MyVendor\Deliverrando\Domain\Repository\PersonRepository
     */
    private $personRepository;

    /**
     * @var \MyVendor\Deliverrando\Domain\Repository\ProductRepository
     */
    private $productRepository;

    /**
     * @param \MyVendor\Deliverrando\Domain\Repository\OrderRepository $orderRepository
     * @return void
     */
    public function injectOrderRepository(OrderRepository $orderRepository) : void
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Repository\PersonRepository $personRepository
     * @return void
     */
    public function injectPersonRepository(PersonRepository $personRepository) : void
    {
        $this->personRepository = $personRepository;
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Repository\ProductRepository $productRepository
     * @return void
     */
    public function injectProductRepository(ProductRepository $productRepository) : void
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Person $loggedInPerson
     * @return \MyVendor\Deliverrando\Domain\Model\Order
     */
    private function setupOrderFromPostArguments(Person $loggedInPerson) : \MyVendor\Deliverrando\Domain\Model\Order
    {
        $order = new \MyVendor\Deliverrando\Domain\Model\Order($loggedInPerson);

        $deliverytime = 0;

        for($i = 0; GeneralUtility::_POST('products' . $i) !== null; ++$i) {
            $product = $this->productRepository->findOneByName(GeneralUtility::_POST('products' . $i));
            $productQuantity = GeneralUtility::_POST('quantity' . $i);
            assert($product !== null);

            if($product->getDeliverytime() > $deliverytime) {
                $deliverytime = $product->getDeliverytime();
            }

            $order->addProductDescription($product, $productQuantity);
        }
        $order->setDeliverytime($deliverytime);

        return $order;
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Person $loggedInPerson
     * @param int $deliveryTime
     * @param string $productNameList
     * @return void
     */
    private function sendEmail(Person $loggedInPerson, int $deliveryTime, string $productNameList) : void
    {
        $email = GeneralUtility::makeInstance(MailMessage::class);
        $email->setCharset('UTF-8');
        $email->setSubject("Delieverrando Bestellung");
        $email->setFrom(['order@delieverrando.com' => 'Delieverrando']);
        $email->setTo([$loggedInPerson->getEmail() => $loggedInPerson->getName()]);
        $email->setBody("<h4>Du hast Essen bestellt!</h4>" . "<p>Es wird in: " . $deliveryTime . ($deliveryTime === 1 ? " Minute " : " Minuten ")
            ."geliefert!<br />Bestellzusammenfassung:</p>" . $productNameList, ENT_QUOTES | ENT_HTML5 | ENT_DISALLOWED | ENT_SUBSTITUTE,
            'UTF-8');
        $email->send();
    }

    /**
     * @param string $deliverrandoAddress
     * @param string $personAddress
     * @param string $apiKey
     * @return int
     */
    private function getTravelTime(string $deliverrandoAddress, string $personAddress, string $apiKey) : int
    {
        $bingMapsRestApiHelper = GeneralUtility::makeInstance(ObjectManager::class)->get(BingMapsRestApiHelper::class);
        $json = $bingMapsRestApiHelper->makeApiCall('/Routes?wayPoint.1='. $deliverrandoAddress .
            '&wayPoint.2=' . $personAddress . '&optimizeWaypoints=true&routeAttributes=all', $apiKey);
        if($json === 'InvalidStatusCode') {
            return -1;
        }
        assert($json->resourceSets[0]->estimatedTotal === 1);

        $travelDuration = $json->resourceSets[0]->resources[0]->travelDuration / 60;

        return $travelDuration;
    }

    /**
     * @param array $productDescs
     * @return object
     */
    private function getProductNameListAndUpdateQuantity(array $productDescs) : object
    {
        $productNameList = '<ul>';
        $quantitySum = 0;

        foreach($productDescs as $productDesc) {
            $product = $productDesc->getProduct();
            $productNameList .= '<li>x' . $productDesc->getQuantity() . ' ' . $product->getName() . '</li>';
            $product->setQuantity($product->getQuantity() - $productDesc->getQuantity());
//            if($product->getQuantity() < 0) {
//            }
            $this->productRepository->update($productDesc->getProduct());

            $quantitySum += $productDesc->getQuantity();
        }
        $productNameList .= '</ul>';

        return (object) ['productNameList' => $productNameList, 'quantitySum' => $quantitySum];
    }

    /**
     * @param string $apiKey
     * @return array
     */
    public function createOrderAndJson(string $apiKey) : array
    {
        assert($GLOBALS['TSFE']->fe_user->getKey('ses', 'uid') !== null);

        $loggedInPerson = $this->personRepository->findByUid($GLOBALS['TSFE']->fe_user->getKey('ses', 'uid'));
        assert($loggedInPerson !== null);

        $order = $this->setupOrderFromPostArguments($loggedInPerson);
        $productDescs = $order->getProductDescriptions();

        $deliverrandoAddress = $productDescs[0]->getProduct()->getDelieverrando()->getAddress();
        $personAddress = $loggedInPerson->getAddress();
        $travelDuration = $this->getTravelTime($deliverrandoAddress, $personAddress, $apiKey);

        if($travelDuration === -1) {
            return  [
                'error' => 'error'
            ];
        }
        $order->setDeliverytime($order->getDeliverytime() + $travelDuration);
        $this->orderRepository->add($order);

        $object = $this->getProductNameListAndUpdateQuantity($productDescs);

        GeneralUtility::makeInstance(ObjectManager::class)->get(PersistenceManager::class)->persistAll();

        $this->sendEmail($loggedInPerson, $order->getDeliverytime(), $object->productNameList);

        return [
            'deliverytime' => $order->getDeliverytime(),
            'orderUid' => $order->getUid(),
            'quantitySum' => $object->quantitySum,
        ];
    }
}