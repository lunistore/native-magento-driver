<?php

namespace Luni\Component\MagentoDriver\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Luni\Component\MagentoDriver\Broker\ProductAttributeValueRepositoryBrokerInterface;
use Luni\Component\MagentoDriver\Entity\ProductInterface;
use Luni\Component\MagentoDriver\Model\AttributeInterface;
use Luni\Component\MagentoDriver\Model\AttributeValueInterface;

class ProductAttributeValueRepositoryFacade
    implements ProductAttributeValueRepositoryInterface
{
    /**
     * @var ProductAttributeValueRepositoryBrokerInterface
     */
    private $broker;

    /**
     * @param ProductAttributeValueRepositoryBrokerInterface $broker
     */
    public function __construct(
        ProductAttributeValueRepositoryBrokerInterface $broker
    ) {
        $this->broker = $broker;
    }

    /**
     * @param ProductInterface $product
     * @return Collection|AttributeValueInterface[]
     */
    public function findAllByProduct(ProductInterface $product)
    {
        $valuesList = [];
        foreach ($this->broker->walkRepositoryList() as $repository) {
            $valuesList = array_merge(
                $valuesList,
                $repository->findAllByProduct($product)->toArray()
            );
        }

        return new ArrayCollection($valuesList);
    }

    /**
     * @param ProductInterface $product
     * @return Collection|AttributeValueInterface[]
     */
    public function findAllVariantAxisByProductFromDefault(ProductInterface $product)
    {
        $valuesList = [];
        foreach ($this->broker->walkRepositoryList() as $repository) {
            $valuesList = array_merge(
                $valuesList,
                $values = $repository->findAllVariantAxisByProductFromDefault($product)->toArray()
            );
        }

        return new ArrayCollection($valuesList);
    }

    /**
     * @param ProductInterface $product
     * @param int $storeId
     * @return Collection|AttributeValueInterface[]
     */
    public function findAllVariantAxisByProductFromStoreId(ProductInterface $product, $storeId)
    {
        $valuesList = [];
        foreach ($this->broker->walkRepositoryList() as $repository) {
            $valuesList = array_merge(
                $valuesList,
                $values = $repository->findAllVariantAxisByProductFromStoreId($product, $storeId)->toArray()
            );
        }

        return new ArrayCollection($valuesList);
    }

    public function findAllByProductAndAttributeListFromDefault(
        ProductInterface $product,
        array $attributeList
    ) {
        $valuesList = [];
        foreach ($this->broker->walkRepositoryList() as $repository) {
            $valuesList = array_merge(
                $valuesList,
                $values = $repository->findAllByProductAndAttributeListFromDefault($product, $attributeList)->toArray()
            );
        }

        return new ArrayCollection($valuesList);
    }

    public function findAllByProductAndAttributeListFromStoreId(
        ProductInterface $product,
        array $attributeList,
        $storeId
    ) {
        $valuesList = [];
        foreach ($this->broker->walkRepositoryList() as $repository) {
            $valuesList = array_merge(
                $valuesList,
                $values = $repository->findAllByProductAndAttributeListFromStoreId($product, $attributeList, $storeId)->toArray()
            );
        }

        return new ArrayCollection($valuesList);
    }

    /**
     * @param ProductInterface $product
     * @param AttributeInterface $attribute
     * @return AttributeValueInterface|null
     */
    public function findOneByProductAndAttributeFromDefault(
        ProductInterface $product,
        AttributeInterface $attribute
    ) {
        foreach ($this->broker->walkRepositoryList() as $matcher => $repository) {
            if (!$matcher($attribute)) {
                continue;
            }

            return $repository->findOneByProductAndAttributeFromDefault($product, $attribute);
        }

        return null;
    }

    /**
     * @param ProductInterface $product
     * @param AttributeInterface $attribute
     * @param int $storeId
     * @return AttributeValueInterface|null
     */
    public function findOneByProductAndAttributeFromStoreId(
        ProductInterface $product,
        AttributeInterface $attribute,
        $storeId
    ) {
        foreach ($this->broker->walkRepositoryList() as $matcher => $repository) {
            if (!$matcher($attribute)) {
                continue;
            }

            return $repository->findOneByProductAndAttributeFromStoreId($product, $attribute, $storeId);
        }

        return null;
    }

    public function findAllByProductListFromDefault(
        array $productList
    ) {
        $valuesList = [];
        foreach ($this->broker->walkRepositoryList() as $repository) {
            $valuesList = array_merge(
                $valuesList,
                $values = $repository->findAllByProductListFromDefault($productList)->toArray()
            );
        }

        return new ArrayCollection($valuesList);
    }

    public function findAllByProductListFromStoreId(
        array $productList,
        $storeId
    ) {
        $valuesList = [];
        foreach ($this->broker->walkRepositoryList() as $repository) {
            $valuesList = array_merge(
                $valuesList,
                $values = $repository->findAllByProductListFromStoreId($productList, $storeId)->toArray()
            );
        }

        return new ArrayCollection($valuesList);
    }

    public function findAllByProductListAndAttributeListFromDefault(
        array $productList,
        array $attributeList
    ) {
        $valuesList = [];
        foreach ($this->broker->walkRepositoryList() as $repository) {
            $valuesList = array_merge(
                $valuesList,
                $values = $repository->findAllByProductListAndAttributeListFromDefault($productList, $attributeList)->toArray()
            );
        }

        return new ArrayCollection($valuesList);
    }

    public function findAllByProductListAndAttributeListFromStoreId(
        array $productList,
        array $attributeList,
        $storeId
    ) {
        $valuesList = [];
        foreach ($this->broker->walkRepositoryList() as $repository) {
            $valuesList = array_merge(
                $valuesList,
                $values = $repository->findAllByProductListAndAttributeListFromStoreId($productList, $attributeList, $storeId)->toArray()
            );
        }

        return new ArrayCollection($valuesList);
    }

    /**
     * @param ProductInterface $product
     * @return Collection|AttributeValueInterface[]
     */
    public function findAllByProductFromDefault(ProductInterface $product)
    {
        $valuesList = [];
        foreach ($this->broker->walkRepositoryList() as $repository) {
            $valuesList = array_merge(
                $valuesList,
                $repository->findAllByProductFromDefault($product)->toArray()
            );
        }

        return new ArrayCollection($valuesList);
    }

    /**
     * @param ProductInterface $product
     * @param int $storeId
     * @return Collection|AttributeValueInterface[]
     */
    public function findAllByProductFromStoreId(ProductInterface $product, $storeId)
    {
        $valuesList = [];
        foreach ($this->broker->walkRepositoryList() as $repository) {
            $valuesList = array_merge(
                $valuesList,
                $values = $repository->findAllByProductFromStoreId($product, $storeId)->toArray()
            );
        }

        return new ArrayCollection($valuesList);
    }
}