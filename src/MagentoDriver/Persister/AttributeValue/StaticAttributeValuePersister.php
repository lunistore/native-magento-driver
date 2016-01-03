<?php

namespace Luni\Component\MagentoDriver\Persister\AttributeValue;

use Luni\Component\MagentoDriver\Model\AttributeValueInterface;
use Luni\Component\MagentoDriver\Entity\ProductInterface;

class StaticAttributeValuePersister
    implements AttributeValuePersisterInterface
{
    public function persist(ProductInterface $product, AttributeValueInterface $value)
    {
    }

    public function initialize()
    {
    }

    public function flush()
    {
    }
}