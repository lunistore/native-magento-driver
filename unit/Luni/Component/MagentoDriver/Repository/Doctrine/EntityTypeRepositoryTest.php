<?php

namespace unit\Luni\Component\MagentoDriver\Repository\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Luni\Component\MagentoDriver\Factory\StandardEntityTypeFactory;
use Luni\Component\MagentoDriver\Model\EntityTypeInterface;
use Luni\Component\MagentoDriver\QueryBuilder\Doctrine\EntityTypeQueryBuilder;
use Luni\Component\MagentoDriver\Repository\Doctrine\EntityTypeRepository;
use Luni\Component\MagentoDriver\Repository\EntityTypeRepositoryInterface;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use unit\Luni\Component\MagentoDriver\SchemaBuilder\DoctrineSchemaBuilder;
use unit\Luni\Component\MagentoDriver\DoctrineTools\DatabaseConnectionAwareTrait;

class EntityTypeRepositoryTest extends \PHPUnit_Framework_TestCase
{
    use DatabaseConnectionAwareTrait;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var EntityTypeRepositoryInterface
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
            $platform->getTruncateTableSQL('eav_entity_type')
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
        $schemaBuilder->ensureFamilyTable();
        $schemaBuilder->ensureEntityTypeTable();

        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($currentSchema, $this->schema);

        foreach ($schemaDiff->toSql($this->getDoctrineConnection()->getDatabasePlatform()) as $sql) {
            $this->getDoctrineConnection()->exec($sql);
        }

        $this->truncateTables();
        $schemaBuilder->hydrateEntityTypeTable('1.9', 'ce');

        $this->repository = new EntityTypeRepository(
            $this->getDoctrineConnection(),
            new EntityTypeQueryBuilder(
                $this->getDoctrineConnection(),
                EntityTypeQueryBuilder::getDefaultTable(),
                EntityTypeQueryBuilder::getDefaultFields()
            ),
            new StandardEntityTypeFactory()
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
        $entityType = $this->repository->findOneById(4);
        $this->assertInstanceOf(EntityTypeInterface::class, $entityType);

        $this->assertEquals($entityType->getId(), '4');
        $this->assertEquals($entityType->getCode(), 'catalog_product');
    }

    public function testFetchingOneByIdButNonExistent()
    {
        $this->assertNull($this->repository->findOneById(123));
    }

    public function testFetchingOneByCode()
    {
        $entityType = $this->repository->findOneByCode('catalog_product');
        $this->assertInstanceOf(EntityTypeInterface::class, $entityType);

        $this->assertEquals($entityType->getCode(), 'catalog_product');
        $this->assertEquals($entityType->getId(), 4);
    }

    public function testFetchingOneByCodeButNonExistent()
    {
        $this->assertNull($this->repository->findOneByCode('Non existent'));
    }
}
