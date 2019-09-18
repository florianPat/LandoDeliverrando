<?php

namespace MyVendor\Deliverrando\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Order extends AbstractEntity
{
    /**
     * @var \MyVendor\Deliverrando\Domain\Model\Person
     */
    protected $person;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MyVendor\Deliverrando\Domain\Model\Product>
     */
    protected $products;

    /**
     * @var string
     */
    protected $productquantities;

    /**
     * @var string
     */
    protected $productprogress;

    /**
     * @var int
     */
    protected $deliverytime;

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Person $person
     */
    public function __construct(\MyVendor\Deliverrando\Domain\Model\Person $person = null)
    {
        $this->person = $person;
        $this->products = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->productquantities = '';
        $this->productprogress = '';
        $this->deliverytime = 0;
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Person $person
     * @return void
     */
    public function setPerson(\MyVendor\Deliverrando\Domain\Model\Person $person) : void
    {
        $this->person = $person;
    }

    /**
     * @param int $productIndex
     * @param bool $checked
     * @return void
     *
     */
    public function alterProgress(int $productIndex, bool $checked) : void
    {
        $productprogressArray = explode(',', $this->productprogress);
        $length = count($productprogressArray);
        $productprogressArray[$productIndex] += $checked ? 1 : -1;
        assert($productprogressArray[$productIndex] >= 0 && $productprogressArray[$productIndex] <= explode(',', $this->productquantities)[$productIndex]);

        $this->productprogress = '';

        assert($length > 0);

        $this->productprogress .= $productprogressArray[0];
        for($i = 1; $i < $length; ++$i) {
            $this->productprogress .= ',' . $productprogressArray[$i];
        }
    }

    /**
     * @return array
     */
    public function getProgress() : array
    {
        return explode(',', $this->productprogress);
    }

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Product $product
     * @return void
     */
    private function addProduct(\MyVendor\Deliverrando\Domain\Model\Product $product) : void
    {
        $this->products->attach($product);
        if($this->productprogress !== '') {
            $this->productprogress .= ',0';
        } else {
            $this->productprogress .= '0';
        }
    }

    /**
     * @param int $quantity
     * @return void
     */
    private function addQuantity(int $quantity) : void
    {
        if($this->productquantities === '') {
            $this->productquantities .= $quantity;
        } else {
            $this->productquantities .= ',' . $quantity;
        }
    }

    /**
     * @param Product $product
     * @param int $quantity
     * @return void
     */
    public function addProductDescription(\MyVendor\Deliverrando\Domain\Model\Product $product, int $quantity) : void
    {
        $this->addProduct($product);
        $this->addQuantity($quantity);
    }

    /**
     * @return array
     */
    public function getProductDescriptions() : array
    {
        $result = [];

        $productQuantities = explode(',', $this->productquantities);
        $productArray = $this->products->toArray();
        $length = count($productQuantities);
        for($i = 0; $i < $length; ++$i) {
            $product = $productArray[$i];
            assert($product);
            $quantity = intval($productQuantities[$i]);

            array_push($result, new Helper\ProductDescription($product, $quantity));
        }

        return $result;
    }

    /**
     * @param int $deliverytime
     * @return void
     */
    public function setDeliverytime(int $deliverytime) : void
    {
        $this->deliverytime = $deliverytime;
    }

    /**
     * @return \MyVendor\Deliverrando\Domain\Model\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return int
     */
    public function getDeliverytime()
    {
        return $this->deliverytime;
    }
}