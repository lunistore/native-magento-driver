<?php

namespace Luni\Component\MagentoDriver\Repository\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Luni\Component\MagentoDriver\Attribute\Attribute;
use Luni\Component\MagentoDriver\Attribute\AttributeInterface;
use Luni\Component\MagentoDriver\AttributeBackend\BackendInterface;
use Luni\Component\MagentoDriver\AttributeBackend\DatetimeAttributeBackend;
use Luni\Component\MagentoDriver\AttributeBackend\DecimalAttributeBackend;
use Luni\Component\MagentoDriver\AttributeBackend\IntegerAttributeBackend;
use Luni\Component\MagentoDriver\AttributeBackend\TextAttributeBackend;
use Luni\Component\MagentoDriver\AttributeBackend\VarcharAttributeBackend;
use Luni\Component\MagentoDriver\AttributeBackend\MediaGalleryAttributeBackend;
use Luni\Component\MagentoDriver\Family\FamilyInterface;
use Luni\Component\MagentoDriver\Entity\ProductInterface;
use Luni\Component\MagentoDriver\Exception\DatabaseFetchingFailureException;
use Luni\Component\MagentoDriver\QueryBuilder\Doctrine\ProductAttributeQueryBuilderInterface;
use Luni\Component\MagentoDriver\Repository\ProductAttributeRepositoryInterface;

/**
 * Class ProductAttributeRepository
 * @package Luni\Component\MagentoDriver\Repository\Doctrine
 */
class ProductAttributeRepository
    implements ProductAttributeRepositoryInterface
{
    /**
     * @var ProductAttributeQueryBuilderInterface
     */
    protected $queryBuilder;

    /**
     * @var Collection|BackendInterface[]
     */
    protected $backends;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Collection
     */
    protected $attributeCacheByCode;

    /**
     * @var Collection
     */
    protected $attributeCacheById;

    /**
     * ProductAttributeRepository constructor.
     * @param Connection $connection
     * @param ProductAttributeQueryBuilderInterface $queryBuilder
     */
    public function __construct(
        Connection $connection,
        ProductAttributeQueryBuilderInterface $queryBuilder
    ) {
        $this->connection = $connection;
        $this->queryBuilder = $queryBuilder;

        $this->attributeCacheByCode = new ArrayCollection();
        $this->attributeCacheById = new ArrayCollection();

        $this->backends = new ArrayCollection();

        $this->backends->set('datetime', new DatetimeAttributeBackend($this->connection, 'catalog_product_entity_datetime'));
        $this->backends->set('decimal',  new DecimalAttributeBackend($this->connection, 'catalog_product_entity_decimal'));
        $this->backends->set('integer',  new IntegerAttributeBackend($this->connection, 'catalog_product_entity_integer'));
        $this->backends->set('text',     new TextAttributeBackend($this->connection, 'catalog_product_entity_text'));
        $this->backends->set('varchar',  new VarcharAttributeBackend($this->connection, 'catalog_product_entity_varchar'));

        /*
        $this->backends->set('media_gallery', new MediaGalleryAttributeBackend(
            $this->connection,
            new Filesystem(new Local('/')),
            '/',
            'catalog_product_entity_media_gallery',
            'catalog_product_entity_media_gallery_value'
        ));
        */
    }

    /**
     * @param array $options
     * @return AttributeInterface
     */
    protected function createNewAttributeInstanceFromDatabase(array $options)
    {
        $attributeId = isset($options['attribute_id']) ? $options['attribute_id'] : null;
        $attributeCode = isset($options['attribute_code']) ? $options['attribute_code'] : null;
        $backendType = isset($options['backend_type']) ? $options['backend_type'] : null;
        $frontendInput = isset($options['frontend_input']) ? $options['frontend_input'] : null;

        unset(
            $options['attribute_id'],
            $options['attribute_code']
        );

        if ($backendType === 'varchar' && $frontendInput === 'gallery') {
            $backend = $this->backends->get('media_gallery');
        } else {
            $backend = $this->backends->get($backendType);
        }

        $attribute = Attribute::buildNewWith($attributeId, $attributeCode, $backend, $options);

        $this->attributeCacheByCode->set($attributeCode, $attribute);
        $this->attributeCacheById->set($attributeId, $attribute);

        return $attribute;
    }

    /**
     * @param string $code
     * @return AttributeInterface
     */
    public function findOneByCode($code)
    {
        if ($this->attributeCacheByCode->containsKey($code)) {
            return $this->attributeCacheByCode->get($code);
        }

        $query = $this->queryBuilder->createFindOneByCodeQueryBuilder('a', 'e');

        $statement = $this->connection->prepare($query);
        if (!$statement->execute([$code])) {
            throw new DatabaseFetchingFailureException();
        }

        if ($statement->rowCount() < 1) {
            return null;
        }

        $options = $statement->fetch();
        return $this->createNewAttributeInstanceFromDatabase($options);
    }

    /**
     * @param int $id
     * @return AttributeInterface
     */
    public function findOneById($id)
    {
        if ($this->attributeCacheById->containsKey($id)) {
            return $this->attributeCacheById->get($id);
        }

        $query = $this->queryBuilder->createFindOneByIdQueryBuilder('a', 'e');

        $statement = $this->connection->prepare($query);
        if (!$statement->execute([$id])) {
            throw new DatabaseFetchingFailureException();
        }

        if ($statement->rowCount() < 1) {
            return null;
        }

        $options = $statement->fetch();
        return $this->createNewAttributeInstanceFromDatabase($options);
    }

    /**
     * @param array|string[] $codeList
     * @return Collection|AttributeInterface[]
     */
    public function findAllByCode(array $codeList)
    {
        $attributeList = new ArrayCollection();
        $codeSearch = [];
        foreach ($codeList as $code) {
            if (!$this->attributeCacheByCode->containsKey($code)) {
                $codeSearch[] = $code;
                continue;
            }

            $attributeList->set($code, $this->attributeCacheByCode->get($code));
        }

        if (count($codeSearch) <= 0) {
            return $attributeList;
        }

        $query = $this->queryBuilder->createFindAllByCodeQueryBuilder('a', 'e', $codeSearch);

        $statement = $this->connection->prepare($query);
        if (!$statement->execute($codeSearch)) {
            throw new DatabaseFetchingFailureException();
        }

        if ($statement->rowCount() < 1) {
            return $attributeList;
        }

        foreach ($statement as $options) {
            $attributeList->set($options['attribute_code'], $this->createNewAttributeInstanceFromDatabase($options));
        }

        return $attributeList;
    }

    /**
     * @param array|int[] $idList
     * @return Collection|AttributeInterface[]
     */
    public function findAllById(array $idList)
    {
        $attributeList = new ArrayCollection();
        $idSearch = [];
        foreach ($idList as $id) {
            if (!$this->attributeCacheById->containsKey($id)) {
                $idSearch[] = $id;
                continue;
            }

            $attributeList->set($id, $this->attributeCacheById->get($id));
        }

        if (count($idSearch) <= 0) {
            return $attributeList;
        }

        $query = $this->queryBuilder->createFindAllByIdQueryBuilder('a', 'e', $idSearch);

        $statement = $this->connection->prepare($query);
        if (!$statement->execute($idSearch)) {
            throw new DatabaseFetchingFailureException();
        }

        $attributeList = new ArrayCollection();
        if ($statement->rowCount() < 1) {
            return $attributeList;
        }

        foreach ($statement as $options) {
            $attributeList->set($options['attribute_code'], $this->createNewAttributeInstanceFromDatabase($options));
        }

        return $attributeList;
    }

    /**
     * @return Collection|AttributeInterface[]
     */
    public function findAll()
    {
        $query = $this->queryBuilder->createFindAllQueryBuilder('a', 'e');

        $statement = $this->connection->prepare($query);
        if (!$statement->execute()) {
            throw new DatabaseFetchingFailureException();
        }

        $attributeList = new ArrayCollection();
        if ($statement->rowCount() < 1) {
            return $attributeList;
        }

        foreach ($statement as $options) {
            $attributeList->set($options['attribute_code'], $this->createNewAttributeInstanceFromDatabase($options));
        }

        return $attributeList;
    }

    /**
     * @param ProductInterface $product
     * @return Collection|AttributeInterface[]
     */
    public function findAllByEntity(ProductInterface $product)
    {
        return $this->findAllByFamily($product->getFamily());
    }

    /**
     * @param ProductInterface $product
     * @return Collection|AttributeInterface[]
     */
    public function findAllVariantAxisByEntity(ProductInterface $product)
    {
        $attributeList = new ArrayCollection();
        if (!$product->isConfigurable()) {
            return $attributeList;
        }

        $query = $this->queryBuilder->createFindAllVariantAxisByEntityQueryBuilder('a', 'e', 'va');

        $statement = $this->connection->prepare($query);
        if (!$statement->execute([$product->getId()])) {
            throw new DatabaseFetchingFailureException();
        }

        if ($statement->rowCount() < 1) {
            return $attributeList;
        }

        foreach ($statement as $options) {
            $attributeList->set($options['attribute_code'], $this->createNewAttributeInstanceFromDatabase($options));
        }

        return $attributeList;
    }

    /**
     * @param FamilyInterface $family
     * @return Collection|AttributeInterface[]
     */
    public function findAllByFamily(FamilyInterface $family)
    {
        $query = $this->queryBuilder->createFindAllByFamilyQueryBuilder('a', 'e', 'f');

        $attributeList = new ArrayCollection();
        $statement = $this->connection->prepare($query);
        if (!$statement->execute([$family->getId()])) {
            throw new DatabaseFetchingFailureException();
        }

        if ($statement->rowCount() < 1) {
            return $attributeList;
        }

        foreach ($statement as $options) {
            $attributeList->set($options['attribute_code'], $this->createNewAttributeInstanceFromDatabase($options));
        }

        return $attributeList;
    }

    /**
     * @param FamilyInterface $family
     * @return Collection|AttributeInterface[]
     */
    public function findAllMandatoryByFamily(FamilyInterface $family)
    {
        $query = $this->queryBuilder->createFindAllMandatoryByFamilyQueryBuilder('a', 'e', 'f');

        $attributeList = new ArrayCollection();
        $statement = $this->connection->prepare($query);
        if (!$statement->execute([$family->getId()])) {
            throw new DatabaseFetchingFailureException();
        }

        if ($statement->rowCount() < 1) {
            return $attributeList;
        }

        foreach ($statement as $options) {
            $attributeList->set($options['attribute_code'], $this->createNewAttributeInstanceFromDatabase($options));
        }

        return $attributeList;
    }
}