<?php

namespace Luni\Component\MagentoDriver\Persister\AttributeValue;

use Luni\Component\MagentoDriver\ModelValue\AttributeValueInterface;
use Luni\Component\MagentoDriver\ModelValue\VarcharAttributeValueInterface;
use Luni\Component\MagentoDriver\Persister\BaseCsvPersisterTrait;
use Luni\Component\MagentoDriver\Entity\ProductInterface;
use Luni\Component\MagentoDriver\Exception\InvalidAttributePersisterTypeException;

class VarcharAttributePersister
    implements PersisterInterface
{
    use BaseCsvPersisterTrait;

    public function initialize()
    {
    }

    /**
     * @param ProductInterface $product
     * @param AttributeValueInterface $value
     */
    public function persist(ProductInterface $product, AttributeValueInterface $value)
    {
        if (!$value instanceof VarcharAttributeValueInterface) {
            throw new InvalidAttributePersisterTypeException();
        }

        $this->temporaryWriter->persistRow([
            'value_id'       => $value->getId(),
            'entity_type_id' => 4,
            'attribute_id'   => $value->getAttributeId(),
            'store_id'       => $value->getStoreId(),
            'entity_id'      => $product->getId(),
            'value'          => $value->getValue(),
        ]);
    }
}