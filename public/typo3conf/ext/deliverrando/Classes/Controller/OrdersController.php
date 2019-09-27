<?php

namespace MyVendor\Deliverrando\Controller;

use MyVendor\Deliverrando\Domain\Repository\OrderRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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

        $ordersJsonArray = $this->orderRepository->transferAllToJsonArray();

        $this->view->setVariablesToRender(['ordersRoot']);
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