<?php

namespace MyVendor\Deliverrando\Domain\Model\Helper;

use MyVendor\Deliverrando\Domain\Model\Product;

class ProductDescription
{
    /**
     * @var Product
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
    public function __construct(Product $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * @return \MyVendor\Deliverrando\Domain\Model\Product
     */
    public function getProduct() : Product
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