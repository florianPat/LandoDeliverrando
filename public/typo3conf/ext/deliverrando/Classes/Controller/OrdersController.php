<?php

namespace MyVendor\Deliverrando\Controller;

use MyVendor\Deliverrando\Domain\Repository\OrderRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;

class OrdersController extends ActionController
{
    /**
     * @var \MyVendor\Deliverrando\Domain\Repository\OrderRepository
     */
    private $orderRepository;

    /**
     * @param \MyVendor\Deliverrando\Domain\Repository\OrderRepository $orderRepository
     * @return void
     */
    public function injectOrderRepository(OrderRepository $orderRepository) : void
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return void
     */
    public function indexAction() : void
    {

    }

    /**
     * @return void
     */
    public function initializeAjaxAction() : void
    {
        $this->defaultViewObjectName = JsonView::class;
    }

    /**
     * @return void
     */
    public function ajaxAction() : void
    {
        assert($GLOBALS['TSFE']->type === 100);

        $this->view->setVariablesToRender(['ordersRoot']);

        $ordersNativeArray = $this->orderRepository->findAll()->toArray();
        $ordersNativeArrayLength = count($ordersNativeArray);
        $ordersJsonArray = [];
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

            array_push($ordersJsonArray, [
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

        $this->view->assignMultiple(['ordersRoot' => [
            'orders' => $ordersJsonArray,
        ]]);
    }

    /**
     * @return void
     */
    public function initializeProgressAction() : void
    {
        $this->defaultViewObjectName = JsonView::class;
    }

    /**
     * @return void
     */
    public function updateProgressAction() : void
    {
        assert($GLOBALS['TSFE']->type === 100);

        $order = $this->orderRepository->findByUid(GeneralUtility::_POST('orderUid'));
        $order->alterProgress(GeneralUtility::_POST('productIndex'), GeneralUtility::_POST('checked'));
        $this->orderRepository->update($order);
    }

    /**
     * @return void
     */
    public function initializeFinishAction() : void
    {
        $this->defaultViewObjectName = JsonView::class;
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Order $order
     * @return void
     */
    public function finishAction(\MyVendor\Deliverrando\Domain\Model\Order $order) : void
    {
        $this->orderRepository->remove($order);
    }
}