<?php

namespace unit\Kiboko\Component\MagentoDriver\SchemaBuilder\Link;

use Doctrine\DBAL\Schema\Schema;

class CatalogProductAttributeValueToEntityType
{
    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var string
     */
    private $backendName;

    /**
     * SchemaBuilder constructor.
     *
     * @param Schema $schema
     * @param string $backendName
     */
    public function __construct(Schema $schema, $backendName)
    {
        $this->schema = $schema;
        $this->backendName = $backendName;
    }

    /**
     * @param string $magentoVersion
     *
     * @return \Doctrine\DBAL\Schema\Table
     */
    public function build($magentoVersion = null)
    {
        $tableName = sprintf('catalog_product_entity_%s', $this->backendName);
        if (!$this->schema->hasTable($tableName) ||
            !$this->schema->hasTable('eav_entity_type')
        ) {
            return;
        }

        $entityTable = $this->schema->getTable($tableName);
        $storeTable = $this->schema->getTable('eav_entity_type');

        $entityTable->addForeignKeyConstraint(
            $storeTable,
            [
                'entity_type_id',
            ],
            [
                'entity_type_id',
            ],
            [
                'onUpdate' => 'CASCADE',
                'onDelete' => 'CASCADE',
            ]
        );
    }
}
