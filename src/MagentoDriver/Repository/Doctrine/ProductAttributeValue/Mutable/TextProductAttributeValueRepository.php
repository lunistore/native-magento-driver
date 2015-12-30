<?php

namespace Luni\Component\MagentoDriver\Repository\Doctrine\ProductAttributeValue\Mutable;

use Luni\Component\MagentoDriver\ModelValue\AttributeValueInterface;
use Luni\Component\MagentoDriver\ModelValue\Mutable\MutableTextAttributeValue;
use Luni\Component\MagentoDriver\Repository\Doctrine\AbstractProductAttributeValueRepository;

class TextProductAttributeValueRepository
    extends AbstractProductAttributeValueRepository
{
    /**
     * @param array $options
     * @return AttributeValueInterface
     */
    protected function createNewAttributeValueInstanceFromDatabase(array $options)
    {
        return new MutableTextAttributeValue(
            $this->attributeRepository->findOneById($options['attribute_id']),
            $options['value'],
            $options['entity_id'],
            $options['store_id']
        );
    }
}