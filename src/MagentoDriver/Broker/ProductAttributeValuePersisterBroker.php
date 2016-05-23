<?php

namespace Kiboko\Component\MagentoDriver\Broker;

use Kiboko\Component\MagentoDriver\Matcher\AttributeValueMatcherInterface;
use Kiboko\Component\MagentoDriver\Model\AttributeInterface;
use Kiboko\Component\MagentoDriver\Persister\AttributeValuePersisterInterface;

class ProductAttributeValuePersisterBroker implements ProductAttributeValuePersisterBrokerInterface
{
    /**
     * @var \SplObjectStorage
     */
    private $backends;

    /**
     * ProductAttributeValuePersisterBroker constructor.
     */
    public function __construct()
    {
        $this->backends = new \SplObjectStorage();
    }

    /**
     * @param AttributeValuePersisterInterface $backend
     * @param AttributeValueMatcherInterface   $matcher
     */
    public function addPersister(
        AttributeValuePersisterInterface $backend,
        AttributeValueMatcherInterface $matcher
    ) {
        $this->backends->attach($matcher, $backend);
    }

    /**
     * @return \Generator|AttributeValuePersisterInterface[]
     */
    public function walkPersisterList()
    {
        foreach ($this->backends as $matcher) {
            yield $matcher => $this->backends[$matcher];
        }
    }

    /**
     * @param AttributeInterface $attribute
     *
     * @return AttributeValuePersisterInterface|null
     */
    public function findFor(AttributeInterface $attribute)
    {
        /**
         * @var AttributeValueMatcherInterface
         * @var AttributeValuePersisterInterface $backend
         */
        foreach ($this->walkPersisterList() as $matcher => $backend) {
            if ($matcher->match($attribute) !== true) {
                continue;
            }

            return $backend;
        }

        return;
    }
}
