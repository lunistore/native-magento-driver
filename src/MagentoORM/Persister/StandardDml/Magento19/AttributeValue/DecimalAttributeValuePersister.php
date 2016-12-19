<?php
/**
 * Copyright (c) 2016 Kiboko SAS.
 *
 * @author Grégory Planchat <gregory@kiboko.fr>
 */

namespace Kiboko\Component\MagentoORM\Persister\StandardDml\V1_9ce\AttributeValue;

use Doctrine\DBAL\Connection;
use Kiboko\Component\MagentoORM\Model\AttributeValueInterface as BaseAttributeValueInterface;
use Kiboko\Component\MagentoORM\Model\V1_9ce\AttributeValueInterface;
use Kiboko\Component\MagentoORM\Model\DecimalAttributeValueInterface;
use Kiboko\Component\MagentoORM\Persister\AttributeValuePersisterInterface;
use Kiboko\Component\MagentoORM\Exception\InvalidAttributePersisterTypeException;
use Kiboko\Component\MagentoORM\Persister\StandardDml\InsertUpdateAwareTrait;

class DecimalAttributeValuePersister implements AttributeValuePersisterInterface
{
    use InsertUpdateAwareTrait;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var \SplQueue
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
     * @param BaseAttributeValueInterface $value
     */
    public function persist(BaseAttributeValueInterface $value)
    {
        if (!$value instanceof AttributeValueInterface) {
            throw new InvalidAttributePersisterTypeException(sprintf(
                'Invalid attribute value type, expected "%s", got "%s".',
                AttributeValueInterface::class,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }
        if (!$value instanceof DecimalAttributeValueInterface) {
            throw new InvalidAttributePersisterTypeException(sprintf(
                'Invalid attribute value type, expected "%s", got "%s".',
                DecimalAttributeValueInterface::class,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        $this->dataQueue->push($value);
    }

    /**
     * @return \Traversable
     */
    public function flush()
    {
        /** @var DecimalAttributeValueInterface|AttributeValueInterface $value */
        foreach ($this->dataQueue as $value) {
            $this->insertOnDuplicateUpdate($this->connection, $this->tableName,
                [
                    'value_id' => $value->getId(),
                    'entity_type_id' => $value->getEntityTypeId(),
                    'attribute_id' => $value->getAttributeId(),
                    'store_id' => $value->getStoreId(),
                    'entity_id' => $value->getProductId(),
                    'value' => $value->getValue() !== null ? number_format($value->getValue(), 4, '.', '') : null,
                ],
                [
                    'entity_type_id',
                    'attribute_id',
                    'store_id',
                    'entity_id',
                    'value',
                ],
                'value_id'
            );

            if ($value->getId() === null) {
                $value->persistedToId($this->connection->lastInsertId());
                yield $value;
            }
        }
    }

    /**
     * @param BaseAttributeValueInterface $value
     */
    public function __invoke(BaseAttributeValueInterface $value)
    {
        $this->persist($value);
    }
}
