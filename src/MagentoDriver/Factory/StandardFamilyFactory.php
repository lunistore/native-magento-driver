<?php

namespace Kiboko\Component\MagentoDriver\Factory;

use Kiboko\Component\MagentoDriver\Model\Family;
use Kiboko\Component\MagentoDriver\Model\FamilyInterface;

class StandardFamilyFactory implements FamilyFactoryInterface
{
    /**
     * @param array $options
     *
     * @return FamilyInterface
     */
    public function buildNew(array $options)
    {
        return Family::buildNewWith(
            $options['attribute_set_id'],
            $options['attribute_set_name'],
            $options['sort_order']
        );
    }
}
