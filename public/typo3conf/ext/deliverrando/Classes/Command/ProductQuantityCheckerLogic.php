<?php

namespace MyVendor\Deliverrando\Task;

use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class ProductQuantityCheckerLogic //implements \Psr\Log\LoggerAwareInterface
{
    //use LoggerAwareTrait;

    /**
     * @return bool
     */
    public function run() : bool
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $productRepository = $objectManager->get(\MyVendor\Deliverrando\Domain\Repository\ProductRepository::class);

        //$this->logger->info('hello from the scheduler!');
        $allProducts = $productRepository->findAll();
        foreach($allProducts as $product)
        {
            if($product->getQuantity() < 5)
            {
                $product->setQuantity($product->getQuantity() + 10);
                //$this->logger->info("ProductQuantityCheckerTask: quantity is low", ['name' => $product->getName()]);
                $this->productRepository->update($product);
            }
        }

        $objectManager->get(PersistenceManager::class)->persistAll();

        return true;
    }
}