<?php
/**
 * Copyright (c) 2016 Kiboko SAS
 *
 * @author Grégory Planchat <gregory@kiboko.fr>
 */

namespace Kiboko\Component\MagentoMapper\Persister;

interface FamilyPersisterInterface
{
    /**
     * @param string $code
     * @param int $identifier
     */
    public function persist($code, $identifier);

    /**
     * @return void
     */
    public function flush();
}
