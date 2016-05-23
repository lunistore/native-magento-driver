<?php

namespace spec\Kiboko\Component\MagentoDriver\Model\Immutable;

use Kiboko\Component\MagentoDriver\Model\AttributeInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImmutableIntegerAttributeValueSpec extends ObjectBehavior
{
    function it_is_an_ImmutableAttributeValueInterface(AttributeInterface $attribute)
    {
        $this->beConstructedWith($attribute, 1.5);
        $this->shouldImplement('Kiboko\Component\MagentoDriver\Model\Immutable\ImmutableAttributeValueInterface');
    }

    function it_should_contain_integer_value(AttributeInterface $attribute)
    {
        $this->beConstructedWith($attribute, 5);

        $this->getValue()
            ->shouldReturn(5)
        ;
    }
}
