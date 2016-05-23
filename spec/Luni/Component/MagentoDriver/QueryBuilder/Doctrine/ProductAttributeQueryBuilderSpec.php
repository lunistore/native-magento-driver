<?php

namespace spec\Kiboko\Component\MagentoDriver\QueryBuilder\Doctrine;

use Doctrine\DBAL\Connection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProductAttributeQueryBuilderSpec extends ObjectBehavior
{
    function it_is_initializable(Connection $connection)
    {
        $this->beConstructedWith($connection, 'main_table', 'extra_table', 'entity_table', 'variant_axis_table', 'family_table', [], []);
        $this->shouldHaveType('Kiboko\Component\MagentoDriver\QueryBuilder\Doctrine\ProductAttributeQueryBuilder');
    }
}
