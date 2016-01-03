<?php

namespace spec\Luni\Component\MagentoDriver\Writer\Temporary;

use League\Flysystem\File;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StandardTemporaryWriterSpec extends ObjectBehavior
{
    function it_is_initializable(File $file)
    {
        $this->beConstructedWith($file, ';', '"', '"');
        $this->shouldHaveType('Luni\Component\MagentoDriver\Writer\Temporary\StandardTemporaryWriter');
    }

    function it_writes_to_file(File $file)
    {
        $this->beConstructedWith($file, ';', '"', '"');

        $this->persistRow(['test', 123, true]);

        $file->putStream(Argument::type('resource'))->shouldBeCalled();

        $this->flush();
    }
}