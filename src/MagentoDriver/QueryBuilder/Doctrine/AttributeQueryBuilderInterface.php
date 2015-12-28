<?php

namespace Luni\Component\MagentoDriver\QueryBuilder\Doctrine;

use Doctrine\DBAL\Query\QueryBuilder;

interface AttributeQueryBuilderInterface
{
    /**
     * @param string $alias
     * @return QueryBuilder
     */
    public function createQueryBuilder($alias);

    /**
     * @param string $alias
     * @param string $extraAlias
     * @return QueryBuilder
     */
    public function createFindAllQueryBuilder($alias, $extraAlias);

    /**
     * @param string $alias
     * @param string $extraAlias
     * @return QueryBuilder
     */
    public function createFindOneByCodeQueryBuilder($alias, $extraAlias);

    /**
     * @param string $alias
     * @param string $extraAlias
     * @return QueryBuilder
     */
    public function createFindOneByIdQueryBuilder($alias, $extraAlias);

    /**
     * @param string $alias
     * @param string $extraAlias
     * @param array|string[] $codeList
     * @return QueryBuilder
     */
    public function createFindAllByCodeQueryBuilder($alias, $extraAlias, array $codeList);

    /**
     * @param string $alias
     * @param string $extraAlias
     * @param array|int[] $idList
     * @return QueryBuilder
     */
    public function createFindAllByIdQueryBuilder($alias, $extraAlias, array $idList);
}