<?php

namespace MyVendor\Deliverrando\Controller;

use MyVendor\Deliverrando\Controller\Helper\BingMapsRestApiHelper;
use MyVendor\Deliverrando\Controller\Helper\CreateOrderHelper;
use MyVendor\Deliverrando\Controller\Helper\CustomerSessionHelper;
use MyVendor\Deliverrando\Domain\Model\Delieverrando;
use MyVendor\Deliverrando\Domain\Model\Person;
use MyVendor\Deliverrando\Domain\Model\Product;
use MyVendor\Deliverrando\Domain\Repository\CategoryRepository;
use MyVendor\Deliverrando\Domain\Repository\OrderRepository;
use MyVendor\Deliverrando\Domain\Repository\PersonRepository;
use MyVendor\Deliverrando\Domain\Repository\ProductRepository;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class StoreInventoryController extends ActionController
{
    /**
    *  @var \MyVendor\Deliverrando\Domain\Repository\ProductRepository
    */
    private $productRepository;

    /**
     * @var \MyVendor\Deliverrando\Domain\Repository\DelieverrandoRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * NOTE: Has the same effect as declaring the method injectDelieverrandoRepository
     */
    private $delieverrandoRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
    *  @param ProductRepository $productRepository
    *  @return void
    */
    public function injectProductRepository(ProductRepository $productRepository) : void
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param CategoryRepository $categoryRepository
     * @return void
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository) : void
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param PersonRepository $personRepository
     * @return void
     */
    public function injectPersonRepository(PersonRepository $personRepository) : void
    {
        $this->personRepository = $personRepository;
    }

    /**
     * @param OrderRepository $orderRepository
     * @return void
     */
    public function injectOrderRepository(OrderRepository $orderRepository) : void
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param string $isoDatetime
     * @return bool
     */
    private function isOpened(string $isoDatetime) : bool
    {
        $startTimePos = strpos($isoDatetime, 'T') + 1;
        $endTimePos = strpos($isoDatetime, '+');
        $isoTime = substr($isoDatetime, $startTimePos, $endTimePos - $startTimePos);
        $hourEndPos = strpos($isoTime, ':');
        $hour = intval(substr($isoTime, 0, $hourEndPos));
        return ($hour > 8 && $hour < 10);
    }

    /**
     * @return void
     */
    private function persistAll() : void
    {
        $this->objectManager->get(PersistenceManager::class)->persistAll();
    }

    /**
     * @return void
     */
    public function initializeAction() : void
    {
        $actionName = $this->resolveActionMethodName();

        $lastlastAction = $GLOBALS['TSFE']->fe_user->getKey('ses', 'lastlastAction');
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'lastAction', $lastlastAction ?? '');

        $GLOBALS['TSFE']->fe_user->setKey('ses', 'lastlastAction', $actionName);
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Delieverrando $deliverrando
     * @param string $postCode
     * @param string $address
     * @return void
     * @\TYPO3\CMS\Extbase\Annotation\Validate("NumberValidator", param="postCode")
     * @\TYPO3\CMS\Extbase\Annotation\Validate("StringLengthValidator", options={"minimum": 5, "maximum": 5}, param="postCode")
     * @\TYPO3\CMS\Extbase\Annotation\Validate("\MyVendor\Deliverrando\Domain\Validator\PostCodeValidator", param="postCode")
     */
    public function postRegisterDeliverrandoAction(Delieverrando $deliverrando, string $postCode = '', string $address = '') : void
    {
        $this->view->assign('deliverrando', $deliverrando);
        if($postCode !== '') {
            $bingMapsRestApiHelper = GeneralUtility::makeInstance(ObjectManager::class)->get(BingMapsRestApiHelper::class);
            $json = $bingMapsRestApiHelper->getLastResult();
            $this->view->assign('postCode', $postCode . ';' . $json->resourceSets[0]->resources[0]->address->locality);
        }
        if($address !== '') {
            $deliverrando->setAddress($address);
            $this->delieverrandoRepository->update($deliverrando);
            $this->redirect('index');
        }
    }

    /**
     * @param string $messageText
     * @param \MyVendor\Deliverrando\Domain\Model\Product $product
     * @param int $deliverrandoUid
     * @return void
     */
    public function indexAction(string $messageText = '', Product $product = null, int $deliverrandoUid = 0) : void
    {
        $lastAction = $GLOBALS['TSFE']->fe_user->getKey('ses', 'lastAction');
        assert($lastAction !== null);
        $this->view->assign('lastAction', $lastAction);

        if($GLOBALS['TSFE']->fe_user->getKey('ses', 'uid') !== null) {
            $this->view->assign('personLoggedIn', 'true');
            if($lastAction !== 'indexAction') {
                $deliverrandoUid = $GLOBALS['TSFE']->fe_user->getKey('ses', 'dUid');
                assert($deliverrandoUid !== null);
            }
            //if($this->isOpened($context->getPropertyFromAspect('date', 'iso'))) {
                $this->view->assign('opened', true);
            //}
        } else {
            if($GLOBALS['TSFE']->fe_user->getKey('ses', 'dUid') !== null) {
                $GLOBALS['TSFE']->fe_user->setKey('ses', 'dUid', null);
            }

            $this->view->assign('opened', true);
        }

        $this->view->assign('deliverrandoUid', $deliverrandoUid);

        if($messageText !== '') {
            assert($product !== null);
            $this->view->assign("messageText", $messageText);
            $this->view->assign('messageProduct', $product);

            $this->persistAll();
        }

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $isLoggedIn = false;
        try {
            $isLoggedIn = $objectManager->get(Context::class)->getPropertyFromAspect('frontend.user', 'isLoggedIn');
        } catch (AspectNotFoundException $e) {
            $this->view->assign('errorMsg', 'The user login checker failed :/');
        }

        if($isLoggedIn) {
            $this->view->assign('categoryOptions', $this->categoryRepository->findAllForFormOptions());

            $delieverrandoUids = $this->delieverrandoRepository->findDelieverRandoUidsForUserGroup();
            $deliverrando = $this->delieverrandoRepository->findByUid($delieverrandoUids[0]);
            if($deliverrando->getAddress() !== '') {
                try {
                    $products = $this->productRepository->findAllWithDieverRandoUids($delieverrandoUids);
                } catch (InvalidQueryException $e) {
                    $this->view->assign('errorMsg', 'Could not get the products of your Deliverrando :/');
                }

                $this->view->assign('products', $products);
                $this->view->assign('delieverrandoName', $deliverrando->getName());
            } else {
                $this->redirect('postRegisterDeliverrando', null, null, ['deliverrando' => $deliverrando]);
            }
        } else {
            if($deliverrandoUid === 0) {
                $this->view->assign('deliverrandos', $this->delieverrandoRepository->findAll());
            } else {
                $this->view->assign('products', $this->productRepository->findAllWithDieverRandoUids([$deliverrandoUid]));
                $this->view->assign('delieverrandoName', $this->delieverrandoRepository->findByUid($deliverrandoUid)->getName());
            }
        }
     }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Product $product
     * @return void
     */
    public function showAction(Product $product) : void
    {
        $this->view->assign('product', $product);
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Product $product
     * @return void
     */
    public function removeAction(Product $product) : void
    {
        $this->productRepository->remove($product);

        //NOTE: Druch forward werden die Daten nicht persistet (es wird kein neuer request-response cycle erstellt)
        $this->forward('index', null, null, ['messageText' => 'Removed', 'product' => $product]);
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Product $product
     * @return void
     */
    public function updateAction(Product $product) : void
    {
        $this->productRepository->update($product);
        $this->forward('index', null, null, ['messageText' => 'Updated', 'product' => $product]);
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Product $product
     * @param int $category
     * @return void
     *
     * NOTE: One can use @   TYPO3\CMS\Extbase\Annotation\IgnoreValidation("argument") to irgnore the argument validation that happens
     * automatically.
     * NOTE: Before the action runs, the arguments are validated. The annotations from the properties, the Validator with
     * the name \MyVendor\Deliverrando\Domain\Validator\ClassnameValidator, and the annotations in the action
     */
    public function addAction(Product $product, int $category) : void
    {
        $categoryObj = $this->categoryRepository->findByUid($category);
        if($categoryObj !== null) {
            $product->addCategory($categoryObj);
        }
        $delieverrando = $this->delieverrandoRepository->findByLoggedInFeUser();
        $product->setDelieverrando($delieverrando);

        $delieverrando->addProduct($product);
        $this->delieverrandoRepository->update($delieverrando);

        $this->forward('index', null, null, ['messageText' => 'Added', 'product' => $product]);
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Person $person
     * @\TYPO3\CMS\Extbase\Annotation\Validate("\MyVendor\Deliverrando\Domain\Validator\PersonNamePasswordValidator", param="person")
     * @return void
     */
    public function loginAction(Person $person) : void
    {
        $loginPerson = $this->personRepository->findByName($person->getName());

        $deliverrandoUid = GeneralUtility::_GET('deliverrandoUid');
        assert($deliverrandoUid !== null);

        CustomerSessionHelper::login($loginPerson, intval($deliverrandoUid));

        $this->redirect('index');
    }

    /**
     * @return void
     */
    public function logoutAction() : void
    {
        CustomerSessionHelper::logout();

        $this->redirect('index');
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Person $person
     * @\TYPO3\CMS\Extbase\Annotation\Validate("\MyVendor\Deliverrando\Domain\Validator\PersonRegisterValidator", param="person")
     * @return void
     */
    public function registerAction(Person $person) : void
    {
        $deliverrandoUid = GeneralUtility::_GET('deliverrandoUid');
        assert($deliverrandoUid !== null);
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'dUid', intval($deliverrandoUid));

        $this->view->assign('person', $person);

        $bingMapsRestApiHelper = GeneralUtility::makeInstance(ObjectManager::class)->get(BingMapsRestApiHelper::class);
        $json = $bingMapsRestApiHelper->getLastResult();
        $this->view->assign('postCode', $person->getAddress() . ';' . $json->resourceSets[0]->resources[0]->address->locality);
    }

    /**
     * @return void
     */
    public function initializeRegisterPersonAddressAction() : void
    {
        $propertyMapperConfiguration = $this->arguments['person']->getPropertyMappingConfiguration();
        $propertyMapperConfiguration->allowAllProperties();
        $propertyMapperConfiguration->setTypeConverterOption('TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter',
            \TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, true);
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Person $person
     * @return void
     */
    public function registerPersonAddressAction(Person $person) : void
    {
        $person->hashPassword();

        $this->personRepository->add($person);

        $this->persistAll();

        $GLOBALS['TSFE']->fe_user->setKey('ses','uid', $person->getUid());

        $this->redirect('index');
    }

    /**
     * @return void
     */
    public function initializeEndOrderAction() : void
    {
        $this->defaultViewObjectName = JsonView::class;
    }

    /**
     * @return void
     */
    public function endOrderAction() : void
    {
        assert($GLOBALS['TSFE']->type === 100);

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $jsonResponse = $objectManager->get(CreateOrderHelper::class)->createOrderAndJson($this->settings['bingApiKey']);

        $this->view->setVariablesToRender(['responseRoot']);
        $this->view->assignMultiple(['responseRoot' => $jsonResponse]);
    }

    public function initializeProgressUpdateAction()
    {
        $this->defaultViewObjectName = JsonView::class;
    }

    /**
     * @return void
     */
    public function progressUpdateAction() : void
    {
        $orderUid = intval(GeneralUtility::_POST('orderUid'));
        $order = $this->orderRepository->findByUid($orderUid);
        $progress = ['finished'];
        if($order !== null) {
            $progress = $order->getProgress();
        }

        $this->view->setVariablesToRender(['progressRoot']);
        $this->view->assignMultiple(['progressRoot' => [
            'progress' => $progress,
        ]]);
    }
}
