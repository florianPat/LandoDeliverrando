<?php

namespace MyVendor\Deliverrando\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Delieverrando extends AbstractEntity
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MyVendor\Deliverrando\Domain\Model\Product>
     * This tells extbase to only load the objects if they are needed, and not to load all child objects which are associated with Deliverrando
     * (that would be called Eager-Loading)
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * This tells extbase to delete the products if the Deliverrando gets deleted
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $products;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup $userGroup
     */
    protected $userGroup;

    /**
     * @var string $address
     */
    protected $address;

    /**
     * @param string $name
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup $userGroup
     */
    public function __construct(string $name, FrontendUserGroup $userGroup)
    {
        $this->name = $name;
        $this->products = new ObjectStorage();
        $this->userGroup = $userGroup;
        $this->address = '';
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
      * @param \MyVendor\Deliverrando\Domain\Model\Product $product
      * @return void
      */
    public function addProduct(Product $product) : void
    {
        $this->products->attach($product);
    }

    /**
      * @param \MyVendor\Deliverrando\Domain\Model\Product $product
      * @return void
      */
    public function removeProduct(Product $product) : void
    {
        $this->products->detach($product);
    }

    /**
      * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MyVendor\Deliverrando\Domain\Model\Product>
      * @return void
      */
    public function setProducts(ObjectStorage $products) : void
    {
        $this->products = $products;
    }

    /**
     * @param string $address
     * @return void
     */
    public function setAddress(string $address) : void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAddress() : string
    {
        return $this->address;
    }

    /**
      * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\MyVendor\Deliverrando\Domain\Model\Product>
      */
    public function getProducts() : ObjectStorage
    {
        return $this->products;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup
     */
    public function getUserGroup() : FrontendUserGroup
    {
        return $this->userGroup;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup $userGroup
     * @return void
     */
    public function setUserGroup(FrontendUserGroup $userGroup)
    {
        $this->userGroup = $userGroup;
    }
}