<?php
/**
 * Copyright (c) 2016 Kiboko SAS.
 *
 * @author Grégory Planchat <gregory@kiboko.fr>
 */

namespace unit\Kiboko\Component\MagentoORM\Repository\V1_9ce\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Kiboko\Component\MagentoORM\Entity\Product\ProductInterface;
use Kiboko\Component\MagentoORM\Factory\AttributeValueFactoryInterface;
use Kiboko\Component\MagentoORM\Model\AttributeInterface;
use Kiboko\Component\MagentoORM\Model\DatetimeAttributeValueInterface;
use Kiboko\Component\MagentoORM\Model\V1_9ce\Immutable\ImmutableDatetimeAttributeValue;
use Kiboko\Component\MagentoORM\QueryBuilder\Doctrine\ProductAttributeValueQueryBuilder;
use Kiboko\Component\MagentoORM\Repository\AttributeRepositoryInterface;
use Kiboko\Component\MagentoORM\Repository\Doctrine\ProductAttributeValueRepository;
use Kiboko\Component\MagentoORM\Repository\ProductAttributeValueRepositoryInterface;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use unit\Kiboko\Component\MagentoORM\SchemaBuilder\DoctrineSchemaBuilder;
use unit\Kiboko\Component\MagentoORM\DoctrineTools\DatabaseConnectionAwareTrait;
use unit\Kiboko\Component\MagentoORM\SchemaBuilder\Table\Store as StoreTableSchemaBuilder;

class ProductAttributeDatetimeValueRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use DatabaseConnectionAwareTrait;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var ProductAttributeValueRepositoryInterface
     */
    private $repository;

    /**
     * @var AttributeRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeRepositoryMock;

    /**
     * @return string
     */
    private function getVersion()
    {
        return '2.0';
    }

    /**
     * @return string
     */
    private function getEdition()
    {
        return 'ce';
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        $dataSet = new \PHPUnit_Extensions_Database_DataSet_ArrayDataSet([]);

        return $dataSet;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    private function truncateTables()
    {
        $platform = $this->getDoctrineConnection()->getDatabasePlatform();

        $this->getDoctrineConnection()->exec('SET FOREIGN_KEY_CHECKS=0');
        $this->getDoctrineConnection()->exec(
            $platform->getTruncateTableSQL(StoreTableSchemaBuilder::getTableName($this->getVersion()))
        );

        $this->getDoctrineConnection()->exec(
            $platform->getTruncateTableSQL('eav_attribute')
        );

        $this->getDoctrineConnection()->exec(
            $platform->getTruncateTableSQL('catalog_product_entity')
        );

        $this->getDoctrineConnection()->exec(
            $platform->getTruncateTableSQL('catalog_product_entity_datetime')
        );
        $this->getDoctrineConnection()->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp()
    {
        parent::setUp();

        $currentSchema = $this->getDoctrineConnection()
            ->getSchemaManager()
            ->createSchema()
        ;

        $this->schema = new Schema();

        $schemaBuilder = new DoctrineSchemaBuilder(
            $this->getDoctrineConnection(), $this->schema, $this->getVersion(), $this->getEdition());
        $schemaBuilder->ensureStoreTable();
        $schemaBuilder->ensureAttributeTable();
        $schemaBuilder->ensureCatalogProductEntityTable();
        $schemaBuilder->ensureCatalogProductAttributeValueTable('datetime', 'datetime');
        $schemaBuilder->ensureCatalogProductAttributeValueToStoreLinks('datetime');
        $schemaBuilder->ensureCatalogProductAttributeValueToAttributeLinks('datetime');
        $schemaBuilder->ensureCatalogProductAttributeValueToCatalogProductEntityLinks('datetime');

        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($currentSchema, $this->schema);

        foreach ($schemaDiff->toSql($this->getDoctrineConnection()->getDatabasePlatform()) as $sql) {
            $this->getDoctrineConnection()->exec($sql);
        }

        $this->truncateTables();

        $schemaBuilder->hydrateStoreTable(
            'catalog_product_entity_datetime',
            DoctrineSchemaBuilder::CONTEXT_REPOSITORY
        );

        $schemaBuilder->hydrateAttributeTable(
            'catalog_product_entity_datetime',
            DoctrineSchemaBuilder::CONTEXT_REPOSITORY
        );

        $schemaBuilder->hydrateCatalogProductEntityTable(
            'catalog_product_entity_datetime',
            DoctrineSchemaBuilder::CONTEXT_REPOSITORY
        );

        $schemaBuilder->hydrateCatalogProductAttributeValueTable(
            'datetime',
            'catalog_product_entity_datetime',
            DoctrineSchemaBuilder::CONTEXT_REPOSITORY
        );

        $this->repository = new ProductAttributeValueRepository(
            $this->getDoctrineConnection(),
            new ProductAttributeValueQueryBuilder(
                $this->getDoctrineConnection(),
                ProductAttributeValueQueryBuilder::getDefaultTable('datetime'),
                ProductAttributeValueQueryBuilder::getDefaultVariantAxisTable(),
                ProductAttributeValueQueryBuilder::getDefaultFields()
            ),
            $this->getAttributeRepositoryMock(),
            $this->getAttributeValueFactoryMock()
        );
    }

    protected function tearDown()
    {
        $this->truncateTables();

        parent::tearDown();

        $this->repository = null;
        $this->doctrineConnection = null;
        $this->connection = null;
        $this->pdo = null;
    }

    /**
     * @param int    $id
     * @param string $code
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|AttributeRepositoryInterface
     */
    private function getAttributeMock($id, $code)
    {
        $mock = $this->createMock(AttributeInterface::class);

        $mock->method('getId')
            ->willReturn($id)
        ;

        $mock->method('getCode')
            ->willReturn($code)
        ;

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AttributeRepositoryInterface
     */
    private function getAttributeRepositoryMock()
    {
        $this->attributeRepositoryMock = $mock = $this->createMock(AttributeRepositoryInterface::class);

        return $mock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AttributeValueFactoryInterface
     */
    private function getAttributeValueFactoryMock()
    {
        $mock = $this->createMock(AttributeValueFactoryInterface::class);

        $mock->method('buildNew')
            ->with($this->isInstanceOf(AttributeInterface::class), $this->isType('array'))
            ->willReturnCallback(function ($attribute, $data) {
                return ImmutableDatetimeAttributeValue::buildNewWith(
                    $data['value_id'],
                    $attribute,
                    $data['value'] != null ? \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['value']) : null,
                    null,
                    $data['store_id']
                );
            })
        ;

        return $mock;
    }

    public function testFetchingOneByProductAndAttributeFromDefault()
    {
        /** @var ProductInterface $product */
        $product = $this->createMock(ProductInterface::class);
        $product
            ->method('getId')
            ->willReturn(3)
        ;

        /** @var AttributeInterface $attribute */
        $attribute = $this->getAttributeMock(167, 'release_date');

        $this->attributeRepositoryMock
            ->method('findOneById')
            ->with(167)
            ->willReturn($attribute)
        ;

        /** @var DatetimeAttributeValueInterface $attributeValue */
        $attributeValue = $this->repository->findOneByProductAndAttributeFromDefault($product, $attribute);
        $this->assertInstanceOf(DatetimeAttributeValueInterface::class, $attributeValue);

        $this->assertTrue(
            $attributeValue->getValue() instanceof \DateTimeInterface ||
            is_null($attributeValue->getValue())
        );
    }

    public function testFetchingOneByProductAndAttributeFromDefaultButNonExistent()
    {
        /** @var ProductInterface $product */
        $product = $this->createMock(ProductInterface::class);
        $product
            ->method('getId')
            ->willReturn(PHP_INT_MAX - 1)
        ;

        /** @var AttributeInterface $attribute */
        $attribute = $this->getAttributeMock(167, 'release_date');

        $this->assertNull($this->repository->findOneByProductAndAttributeFromDefault($product, $attribute));
    }
}
