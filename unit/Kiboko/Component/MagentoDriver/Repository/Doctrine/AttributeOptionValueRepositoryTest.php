<?php

namespace unit\Kiboko\Component\MagentoDriver\Repository\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Kiboko\Component\MagentoDriver\Factory\AttributeOptionValueFactory;
use Kiboko\Component\MagentoDriver\Model\AttributeOptionValueInterface;
use Kiboko\Component\MagentoDriver\QueryBuilder\Doctrine\AttributeOptionValueQueryBuilder;
use Kiboko\Component\MagentoDriver\Repository\Doctrine\AttributeOptionValueRepository;
use Kiboko\Component\MagentoDriver\Repository\AttributeOptionValueRepositoryInterface;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use unit\Kiboko\Component\MagentoDriver\SchemaBuilder\DoctrineSchemaBuilder;
use unit\Kiboko\Component\MagentoDriver\DoctrineTools\DatabaseConnectionAwareTrait;

class AttributeOptionValueRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use DatabaseConnectionAwareTrait;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var AttributeOptionValueRepositoryInterface
     */
    private $repository;

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        $dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet();

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
            $platform->getTruncateTableSQL('eav_attribute')
        );

        $this->getDoctrineConnection()->exec(
            $platform->getTruncateTableSQL('eav_attribute_option')
        );

        $this->getDoctrineConnection()->exec(
            $platform->getTruncateTableSQL('core_store')
        );

        $this->getDoctrineConnection()->exec(
            $platform->getTruncateTableSQL('eav_attribute_option_value')
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

        $schemaBuilder = new DoctrineSchemaBuilder($this->getDoctrineConnection(), $this->schema);
        $schemaBuilder->ensureAttributeTable();
        $schemaBuilder->ensureAttributeOptionTable();
        $schemaBuilder->ensureStoreTable();
        $schemaBuilder->ensureAttributeOptionValueTable();

        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($currentSchema, $this->schema);

        foreach ($schemaDiff->toSql($this->getDoctrineConnection()->getDatabasePlatform()) as $sql) {
            $this->getDoctrineConnection()->exec($sql);
        }

        $this->truncateTables();

        $magentoVersion = '1.9';
        $magentoEdition = 'ce';

        $schemaBuilder->hydrateAttributeTable($magentoVersion, $magentoEdition);
        $schemaBuilder->hydrateAttributeOptionTable($magentoVersion, $magentoEdition);
        $schemaBuilder->hydrateStoreTable($magentoVersion, $magentoEdition);
        $schemaBuilder->hydrateAttributeOptionValueTable($magentoVersion, $magentoEdition);

        $this->repository = new AttributeOptionValueRepository(
            $this->getDoctrineConnection(),
            new AttributeOptionValueQueryBuilder(
                $this->getDoctrineConnection(),
                AttributeOptionValueQueryBuilder::getDefaultTable(),
                AttributeOptionValueQueryBuilder::getDefaultFields()
            ),
            new AttributeOptionValueFactory()
        );
    }

    protected function tearDown()
    {
        $this->truncateTables();

        parent::tearDown();

        $this->repository = null;
    }

    public function testFetchingOneById()
    {
        $attributeOptionValue = $this->repository->findOneById(2);
        $this->assertInstanceOf(AttributeOptionValueInterface::class, $attributeOptionValue);

        $this->assertEquals($attributeOptionValue->getId(), 2);
        $this->assertEquals($attributeOptionValue->getOptionId(), 1);
        $this->assertEquals($attributeOptionValue->getStoreId(), 1);
        $this->assertEquals($attributeOptionValue->getValue(), 'Rouge');
    }

    public function testFetchingOneByIdButNonExistent()
    {
        $this->assertNull($this->repository->findOneById(123));
    }
}