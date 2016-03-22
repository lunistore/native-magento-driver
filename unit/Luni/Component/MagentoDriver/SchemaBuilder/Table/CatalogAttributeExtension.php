<?php

namespace unit\Luni\Component\MagentoDriver\SchemaBuilder\Table;

use Doctrine\DBAL\Schema\Schema;

class CatalogAttributeExtension
{
    /**
     * @var Schema
     */
    private $schema;

    /**
     * SchemaBuilder constructor.
     * @param Schema $schema
     */
    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param string $magentoVersion
     * @return \Doctrine\DBAL\Schema\Table
     */
    public function build($magentoVersion = null)
    {
        $table = $this->schema->createTable('catalog_eav_attribute');

        $table->addColumn('attribute_id', 'smallint', ['unsigned' => true]);
        $table->addColumn('frontend_input_renderer', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('is_global', 'smallint', ['unsigned' => true]);
        $table->addColumn('is_visible', 'smallint', ['unsigned' => true]);
        $table->addColumn('is_searchable', 'smallint', ['unsigned' => true]);
        $table->addColumn('is_filterable', 'smallint', ['unsigned' => true]);
        $table->addColumn('is_comparable', 'smallint', ['unsigned' => true]);
        $table->addColumn('is_visible_on_front', 'smallint', ['unsigned' => true]);
        $table->addColumn('is_html_allowed_on_front', 'smallint', ['unsigned' => true]);
        $table->addColumn('is_used_for_price_rules', 'smallint', ['unsigned' => true]);
        $table->addColumn('is_filterable_in_search', 'smallint', ['unsigned' => true]);
        $table->addColumn('used_in_product_listing', 'smallint', ['unsigned' => true]);
        $table->addColumn('used_for_sort_by', 'smallint', ['unsigned' => true]);
        $table->addColumn('is_configurable', 'smallint', ['unsigned' => true]);
        $table->addColumn('apply_to', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('is_visible_in_advanced_search', 'smallint', ['unsigned' => true]);
        $table->addColumn('position', 'integer', ['unsigned' => true]);
        $table->addColumn('is_wysiwyg_enabled', 'smallint', ['unsigned' => true]);
        $table->addColumn('is_used_for_promo_rules', 'smallint', ['unsigned' => true]);

        $table->setPrimaryKey(['attribute_id']);
        $table->addIndex(['used_for_sort_by']);
        $table->addIndex(['used_in_product_listing']);

        return $table;
    }
}