<?php

namespace Kiboko\Component\MagentoMapper\Mapper;

interface MapperInterface
{
    /**
     * @param string $code
     *
     * @return int
     */
    public function map($code);

    /**
     * @param string $code
     * @param int $identifier
     */
    public function persist($code, $identifier);
}
