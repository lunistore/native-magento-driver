<?php

namespace Luni\Component\MagentoDriver\AttributeBackend;

use Doctrine\DBAL\Connection;
use Symfony\Component\Filesystem\Filesystem;
use Luni\Component\MagentoDriver\AttributeValue\AttributeValueInterface;
use Luni\Component\MagentoDriver\AttributeValue\DatetimeAttributeValueInterface;
use Luni\Component\MagentoDriver\Entity\ProductInterface;
use Luni\Component\MagentoDriver\Exception\InvalidAttributeBackendTypeException;

class DatetimeAttributeBackend
    implements BackendInterface
{
    use BaseAttributeCsvBackendTrait;

    /**
     * @param Connection $connection
     * @param string $table
     */
    public function __construct(
        Connection $connection,
        $table
    ) {
        $this->connection = $connection;
        $this->table = $table;

        $this->localFs = new Filesystem();
    }

    /**
     * @param ProductInterface $product
     * @param AttributeValueInterface $value
     */
    public function persist(ProductInterface $product, AttributeValueInterface $value)
    {
        if (!$value instanceof DatetimeAttributeValueInterface) {
            throw new InvalidAttributeBackendTypeException();
        }

        $this->persistRow([
            'value_id'       => $value->getId(),
            'entity_type_id' => 4,
            'attribute_id'   => $value->getAttributeId(),
            'store_id'       => $value->getStoreId(),
            'entity_id'      => $product->getId(),
            'value'          => $value->getValue()->format('Y-m-d H:i:s'),
        ]);
    }
}