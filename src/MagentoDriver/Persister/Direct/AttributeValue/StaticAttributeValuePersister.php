<?php

namespace Kiboko\Component\MagentoDriver\Persister\Direct\AttributeValue;

use Kiboko\Component\MagentoDriver\Model\AttributeValueInterface;
use Kiboko\Component\MagentoDriver\Persister\AttributeValuePersisterInterface;

class StaticAttributeValuePersister implements AttributeValuePersisterInterface
{
    public function initialize()
    {
    }

    /**
     * @param AttributeValueInterface $value
     */
    public function persist(AttributeValueInterface $value)
    {
    }

    /**
     * @param AttributeValueInterface $value
     */
    public function __invoke(AttributeValueInterface $value)
    {
        $this->persist($value);
    }

    public function flush()
    {
    }
}
