<?php

namespace spec\Luni\Component\MagentoDriver\Persister\AttributeValue;

use Luni\Component\MagentoDriver\Broker\AttributePersisterBrokerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AttributeValuePersisterFacadeSpec extends ObjectBehavior
{
    function it_is_initializable(AttributePersisterBrokerInterface $broker)
    {
        $this->beConstructedWith($broker);
        $this->shouldHaveType('Luni\Component\MagentoDriver\Persister\AttributeValue\AttributeValuePersisterFacade');
    }
}