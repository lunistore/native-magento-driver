<?php

namespace Luni\Component\MagentoDriver\Persister\Direct\SuperAttribute;

use Doctrine\DBAL\Connection;
use Luni\Component\MagentoDriver\Model\SuperAttributeInterface;
use Luni\Component\MagentoDriver\Persister\SuperAttributePersisterInterface;

class ProductSuperAttributePersister implements SuperAttributePersisterInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var \SplQueue|SuperAttributeInterface[]
     */
    private $dataQueue;

    /**
     * @param Connection $connection
     * @param string     $tableName
     */
    public function __construct(
        Connection $connection,
        $tableName
    ) {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->dataQueue = new \SplQueue();
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        return $this->tableName;
    }

    public function initialize()
    {
    }

    /**
     * @param SuperAttributeInterface $superAttribute
     */
    public function persist(SuperAttributeInterface $superAttribute)
    {
        $this->dataQueue->push($superAttribute);
    }

    public function flush()
    {
        foreach ($this->dataQueue as $superAttribute) {
            if ($superAttribute->getId()) {
                $this->connection->update($this->tableName,
                    [
                        'product_id' => $superAttribute->getProductId(),
                        'attribute_id' => $superAttribute->getAttributeId(),
                        'position' => $superAttribute->getPosition() ?: 0,
                    ],
                    [
                        'product_super_attribute_id' => $superAttribute->getId(),
                    ]
                );
            } else {
                $this->connection->insert($this->tableName,
                    [
                        'product_id' => $superAttribute->getProductId(),
                        'attribute_id' => $superAttribute->getAttributeId(),
                        'position' => $superAttribute->getPosition() ?: 0,
                    ]
                );

                $superAttribute->persistedToId($this->connection->lastInsertId());
            }
        }
    }

    /**
     * @param SuperAttributeInterface $superAttribute
     */
    public function __invoke(SuperAttributeInterface $superAttribute)
    {
        $this->persist($superAttribute);
    }
}