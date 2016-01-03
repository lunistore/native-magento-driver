<?php

namespace spec\Luni\Component\MagentoDriver\Writer\Database;

use Doctrine\DBAL\Connection;
use League\Flysystem\File;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DataInfileDatabaseWriterSpec extends ObjectBehavior
{
    function it_is_initializable(File $file, Connection $connection)
    {
        $this->beConstructedWith($file, $connection, ';', '"', '"');
        $this->shouldHaveType('Luni\Component\MagentoDriver\Writer\Database\DataInfileDatabaseWriter');
    }
}