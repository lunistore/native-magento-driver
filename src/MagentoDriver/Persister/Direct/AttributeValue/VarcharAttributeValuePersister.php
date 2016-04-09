<?php

namespace Luni\Component\MagentoDriver\Persister\Direct\AttributeValue;

use Doctrine\DBAL\Connection;
use Luni\Component\MagentoDriver\Model\AttributeValueInterface;
use Luni\Component\MagentoDriver\Model\VarcharAttributeValueInterface;
use Luni\Component\MagentoDriver\Persister\AttributeValuePersisterInterface;
use Luni\Component\MagentoDriver\Exception\InvalidAttributePersisterTypeException;

class VarcharAttributeValuePersister implements AttributeValuePersisterInterface
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
     * @var \SplQueue|AttributeValueInterface
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
        $this->dataQueue = new \SplQueue();
    }

    /**
     * @param AttributeValueInterface $value
     */
    public function persist(AttributeValueInterface $value)
    {
        if (!$value instanceof VarcharAttributeValueInterface) {
            throw new InvalidAttributePersisterTypeException(sprintf(
                'Invalid attribute value type for "%s", expected "%s", got "%s".',
                $value->getAttributeCode(),
                VarcharAttributeValueInterface::class,
                get_class($value)
            ));
        }

        $this->dataQueue->push($value);
    }

    public function flush()
    {
        /** @var VarcharAttributeValueInterface $value */
        foreach ($this->dataQueue as $value) {
            $count = 0;
            if ($value->getId()) {
                $count = $this->connection->update($this->tableName,
                    [
                        'entity_type_id' => 4,
                        'attribute_id' => $value->getAttributeId(),
                        'store_id' => $value->getStoreId(),
                        'entity_id' => $value->getProductId(),
                        'value' => $value->getValue(),
                    ],
                    [
                        'value_id' => $value->getId(),
                    ]
                );
            }

            if ($count <= 0) {
                $this->connection->insert($this->tableName,
                    [
                        'value_id' => $value->getId(),
                        'entity_type_id' => 4,
                        'attribute_id' => $value->getAttributeId(),
                        'store_id' => $value->getStoreId(),
                        'entity_id' => $value->getProductId(),
                        'value' => $value->getValue(),
                    ]
                );

                $value->persistedToId($this->connection->lastInsertId());
            }
        }
    }

    /**
     * @param AttributeValueInterface $value
     */
    public function __invoke(AttributeValueInterface $value)
    {
        $this->persist($value);
    }
}
