<?php

namespace spec\Kiboko\Component\MagentoORM\Persister\FlatFile\AttributeValue;

use Kiboko\Component\MagentoORM\Writer\Database\DatabaseWriterInterface;
use Kiboko\Component\MagentoORM\Writer\Temporary\TemporaryWriterInterface;
use PhpSpec\ObjectBehavior;

class DatetimeAttributeValuePersisterSpec extends ObjectBehavior
{
    public function it_is_initializable(
        TemporaryWriterInterface $temporaryWriter,
        DatabaseWriterInterface $databaseWriter
    ) {
        $this->beConstructedWith($temporaryWriter, $databaseWriter, 'table', []);
        $this->shouldHaveType('Kiboko\Component\MagentoORM\Persister\FlatFile\AttributeValue\DatetimeAttributeValuePersister');
    }
}