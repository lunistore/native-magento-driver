<?php
/**
 * Copyright (c) 2016 Kiboko SAS.
 *
 * @author Grégory Planchat <gregory@kiboko.fr>
 */

namespace Kiboko\Component\AkeneoToMagentoMapper\Persister\Doctrine;

use Doctrine\DBAL\Connection;
use Kiboko\Component\AkeneoToMagentoMapper\Persister\AttributeOptionPersisterInterface;

class AttributeOptionPersister implements AttributeOptionPersisterInterface
{
    /**
     * @var Connection
     */
    private $connection;

    private $tableName;

    private $unitOfWork;

    public function __construct(
        Connection $connection,
        $tableName
    ) {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->unitOfWork = [];
    }

    /**
     * @param string $code
     * @param int    $identifier
     */
    public function persist($code, $identifier)
    {
        $this->unitOfWork[] = [
            'option_id' => $identifier,
            'option_code' => $code,
        ];
    }

    public function flush()
    {
        foreach ($this->unitOfWork as $item) {
            $this->connection->insert($this->tableName, $item);
        }
    }
}