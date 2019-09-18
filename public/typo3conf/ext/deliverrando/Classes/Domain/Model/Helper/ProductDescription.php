<?php

namespace MyVendor\Deliverrando\Domain\Model\Helper;

class ProductDescription
{
    /**
     * @var \MyVendor\Deliverrando\Domain\Model\Product
     */
    private $product;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @param \MyVendor\Deliverrando\Domain\Model\Product $product
     * @param int $quantity
     */
    public function __construct(\MyVendor\Deliverrando\Domain\Model\Product $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * @return \MyVendor\Deliverrando\Domain\Model\Product
     */
    public function getProduct() : \MyVendor\Deliverrando\Domain\Model\Product
    {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getQuantity() : int
    {
        return $this->quantity;
    }
}