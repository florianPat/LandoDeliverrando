<?php


namespace MyVendor\Deliverrando\Command;

use MyVendor\Deliverrando\Task\ProductQuantityCheckerLogic;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProductOrderCommand extends Command
{
    protected function configure()
    {
        parent::configure();
        $this->setDescription('Orders new products if necessary.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $businessLogic = GeneralUtility::makeInstance(ProductQuantityCheckerLogic::class);
        return $businessLogic->run() ? 0 : 1;
    }
}