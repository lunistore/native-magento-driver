<?php

namespace unit\Kiboko\Component\MagentoDriver\Persister\Direct\Attribute;

use Doctrine\DBAL\Schema\Schema;
use Kiboko\Component\MagentoDriver\Model\AttributeLabel;
use Kiboko\Component\MagentoDriver\Persister\AttributeLabelPersisterInterface;
use Kiboko\Component\MagentoDriver\Persister\Direct\Attribute\AttributeLabelPersister;
use Kiboko\Component\MagentoDriver\QueryBuilder\Doctrine\AttributeLabelQueryBuilder;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use unit\Kiboko\Component\MagentoDriver\SchemaBuilder\DoctrineSchemaBuilder;
use unit\Kiboko\Component\MagentoDriver\DoctrineTools\DatabaseConnectionAwareTrait;
use unit\Kiboko\Component\MagentoDriver\SchemaBuilder\Fixture\Loader;

class AttributeLabelPersisterTest extends \PHPUnit_Framework_TestCase
{
    use DatabaseConnectionAwareTrait;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var AttributeLabelPersisterInterface
     */
    private $persister;

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        $dataset = new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(
            $this->getFixturesPathname('eav_attribute_label', '1.9', 'ce'));

        return $dataset;
    }

    private function truncateTables()
    {
        $platform = $this->getDoctrineConnection()->getDatabasePlatform();

        $this->getDoctrineConnection()->exec('SET FOREIGN_KEY_CHECKS=0');

        $this->getDoctrineConnection()->exec(
            $platform->getTruncateTableSQL('core_store')
        );

        $this->getDoctrineConnection()->exec(
            $platform->getTruncateTableSQL('eav_attribute')
        );

        $this->getDoctrineConnection()->exec(
            $platform->getTruncateTableSQL('eav_attribute_label')
        );

        $this->getDoctrineConnection()->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp()
    {
        $currentSchema = $this->getDoctrineConnection()->getSchemaManager()->createSchema();

        $this->schema = new Schema();

        $schemaBuilder = new DoctrineSchemaBuilder($this->getDoctrineConnection(), $this->schema);
        $schemaBuilder->ensureStoreTable();
        $schemaBuilder->ensureAttributeTable();
        $schemaBuilder->ensureAttributeLabelTable();

        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($currentSchema, $this->schema);

        foreach ($schemaDiff->toSql($this->getDoctrineConnection()->getDatabasePlatform()) as $sql) {
            $this->getDoctrineConnection()->exec($sql);
        }

        $this->truncateTables();

        parent::setUp();

        $this->persister = new AttributeLabelPersister(
            $this->getDoctrineConnection(),
            AttributeLabelQueryBuilder::getDefaultTable()
        );
    }

    protected function tearDown()
    {
        $this->truncateTables();
        parent::tearDown();

        $this->persister = null;
    }

    public function testInsertNone()
    {
        $this->persister->initialize();
        $this->persister->flush();

        $this->assertTableRowCount('eav_attribute_label', 0);
    }

    public function testInsertOne()
    {
        $dataLoader = new Loader($this->getDoctrineConnection(), 'eav_attribute_label');

        $this->persister->initialize();
        foreach ($dataLoader->walkData('1.9', 'ce') as $data) {
            $attribute = AttributeLabel::buildNewWith(
                $data['attribute_label_id'],
                $data['attribute_id'],
                $data['store_id'],
                $data['value']
            );
            $this->persister->persist($attribute);
        }
        $this->persister->flush();

        $expected = new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(
            $this->getFixturesPathname('eav_attribute_label', '1.9', 'ce'));

        $actual = new \PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
        $actual->addTable('eav_attribute_label');

        $this->assertDataSetsEqual($expected, $actual);
    }

    public function testUpdateOneExisting()
    {
        $dataLoader = new Loader($this->getDoctrineConnection(), 'eav_attribute_label');

        $this->persister->initialize();
        foreach ($dataLoader->walkData('1.9', 'ce') as $data) {
            $attribute = AttributeLabel::buildNewWith(
                $data['attribute_label_id'],
                $data['attribute_id'],
                $data['store_id'],
                $data['value']
            );
            $this->persister->persist($attribute);
        }
        $this->persister->flush();

        $expected = new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(
            $this->getFixturesPathname('eav_attribute_label', '1.9', 'ce'));

        $actual = new \PHPUnit_Extensions_Database_DataSet_QueryDataSet($this->getConnection());
        $actual->addTable('eav_attribute_label');

        $this->assertDataSetsEqual($expected, $actual);
    }
}
