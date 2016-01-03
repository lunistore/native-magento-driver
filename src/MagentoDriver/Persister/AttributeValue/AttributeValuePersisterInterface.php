<?php

namespace Luni\Component\MagentoDriver\Persister\AttributeValue;

use Luni\Component\MagentoDriver\Entity\ProductInterface;
use Luni\Component\MagentoDriver\Model\AttributeValueInterface;

interface AttributeValuePersisterInterface
{
    /**
     * @param ProductInterface $product
     * @param AttributeValueInterface $value
     */
    public function persist(ProductInterface $product, AttributeValueInterface $value);

    /**
     * @return void
     */
    public function initialize();

    /**
     * @return void
     */
    public function flush();
}