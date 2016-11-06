<?php
/**
 * Copyright (c) 2016 Kiboko SAS
 *
 * @author Grégory Planchat <gregory@kiboko.fr>
 */

namespace Kiboko\Component\MagentoMapper\Mapper;

interface AttributeGroupMapperInterface
{
    /**
     * @param string $groupCode
     * @param string $familyCode
     *
     * @return int
     */
    public function map($groupCode, $familyCode);

    /**
     * @param string $groupCode
     * @param string $familyCode
     * @param int $identifier
     */
    public function persist($groupCode, $familyCode, $identifier);

    /**
     * @return void
     */
    public function flush();
}