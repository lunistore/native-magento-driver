<?php

namespace Kiboko\Component\MagentoDriver\Factory;

use Kiboko\Component\MagentoDriver\Model\EntityStore;
use Kiboko\Component\MagentoDriver\Model\EntityStoreInterface;

class StandardEntityStoreFactory implements EntityStoreFactoryInterface
{
    /**
     * @param array $options
     *
     * @return EntityStoreInterface
     */
    public function buildNew(array $options)
    {
        return EntityStore::buildNewWith(
            $options['entity_store_id'],
            $options['entity_type_id'],
            $options['store_id'],
            $options['increment_prefix'],
            $options['increment_last_id']
        );
    }
}
