<?php

namespace Kiboko\Component\MagentoDriver\Persister\StandardDml\Attribute;

use Doctrine\DBAL\Connection;
use Kiboko\Component\MagentoDriver\Exception\RuntimeErrorException;
use Kiboko\Component\MagentoDriver\Model\CatalogAttributeExtensionInterface;
use Kiboko\Component\MagentoDriver\Persister\CatalogAttributeExtensionPersisterInterface;
use Kiboko\Component\MagentoDriver\Persister\StandardDml\InsertUpdateAwareTrait;

class CatalogAttributeExtensionPersister implements CatalogAttributeExtensionPersisterInterface
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
     * @param CatalogAttributeExtensionInterface $attribute
     */
    public function persist(CatalogAttributeExtensionInterface $attribute)
    {
        $this->dataQueue->push($attribute);
    }

    public function flush()
    {
        /** @var CatalogAttributeExtensionInterface $attribute */
        foreach ($this->dataQueue as $attribute) {
            if (!$attribute->getId()) {
                throw new RuntimeErrorException('Attribute #id should be defined.');
            }
            
            $catalogAttributeExtensionDatas = array(
                'ce' => array(
                    '1.9' => array(
                        'attribute_id' => $attribute->getId(),
                        'frontend_input_renderer' => $attribute->getFrontendInputRendererClassName(),
                        'is_global' => $attribute->isGlobal(),
                        'is_visible' => $attribute->isVisible(),
                        'is_searchable' => (int) $attribute->isSearchable(),
                        'is_filterable' => (int) $attribute->isFilterable(),
                        'is_comparable' => (int) $attribute->isComparable(),
                        'is_visible_on_front' => (int) $attribute->isVisibleOnFront(),
                        'is_html_allowed_on_front' => (int) $attribute->isHtmlAllowedOnFront(),
                        'is_used_for_price_rules' => (int) $attribute->isUsedForPriceRules(),
                        'is_filterable_in_search' => (int) $attribute->isFilterableInSearch(),
                        'used_in_product_listing' => (int) $attribute->isUsedInProductListing(),
                        'used_for_sort_by' => (int) $attribute->isUsedForSortBy(),
                        'is_configurable' => (int) $attribute->isConfigurable(),
                        'apply_to' => empty($attribute->getProductTypesApplyingTo()) ?
                            null : implode(',', $attribute->getProductTypesApplyingTo()),
                        'is_visible_in_advanced_search' => (int) $attribute->isVisibleInAdvancedSearch(),
                        'position' => $attribute->getPosition(),
                        'is_wysiwyg_enabled' => (int) $attribute->isWysiwygEnabled(),
                        'is_used_for_promo_rules' => (int) $attribute->isUsedForPromoRules(),
                    ),
                    '2.0' => array(
                        'attribute_id' => $attribute->getId(),
                        'frontend_input_renderer' => $attribute->getFrontendInputRendererClassName(),
                        'is_global' => $attribute->isGlobal(),
                        'is_visible' => $attribute->isVisible(),
                        'is_searchable' => (int) $attribute->isSearchable(),
                        'is_filterable' => (int) $attribute->isFilterable(),
                        'is_comparable' => (int) $attribute->isComparable(),
                        'is_visible_on_front' => (int) $attribute->isVisibleOnFront(),
                        'is_html_allowed_on_front' => (int) $attribute->isHtmlAllowedOnFront(),
                        'is_used_for_price_rules' => (int) $attribute->isUsedForPriceRules(),
                        'is_filterable_in_search' => (int) $attribute->isFilterableInSearch(),
                        'used_in_product_listing' => (int) $attribute->isUsedInProductListing(),
                        'used_for_sort_by' => (int) $attribute->isUsedForSortBy(),
                        'apply_to' => empty($attribute->getProductTypesApplyingTo()) ?
                            null : implode(',', $attribute->getProductTypesApplyingTo()),
                        'is_visible_in_advanced_search' => (int) $attribute->isVisibleInAdvancedSearch(),
                        'position' => $attribute->getPosition(),
                        'is_wysiwyg_enabled' => (int) $attribute->isWysiwygEnabled(),
                        'is_used_for_promo_rules' => (int) $attribute->isUsedForPromoRules(),
                        'is_required_in_admin_store' => (int) $attribute->isRequiredInAdminStore(),
                        'is_used_in_grid' => (int) $attribute->isUsedInGrid(),
                        'is_visible_in_grid' => (int) $attribute->isVisibleInGrid(),
                        'is_filterable_in_grid' => (int) $attribute->isFilterableInGrid(),
                        'search_weight' => (int) $attribute->getSearchWeight(),
                        'additional_data' => empty($attribute->getAdditionalData()) ?
                            null : implode(',', $attribute->getAdditionalData()),
                    )
                )
            );
            
            $catalogAttributeExtensionColumns = array(
                'ce' => array(
                    '1.9' => array(
                        'frontend_input_renderer',
                        'is_global',
                        'is_visible',
                        'is_searchable',
                        'is_filterable',
                        'is_comparable',
                        'is_visible_on_front',
                        'is_html_allowed_on_front',
                        'is_used_for_price_rules',
                        'is_filterable_in_search',
                        'used_in_product_listing',
                        'used_for_sort_by',
                        'is_configurable',
                        'apply_to',
                        'is_visible_in_advanced_search',
                        'position',
                        'is_wysiwyg_enabled',
                        'is_used_for_promo_rules',
                    ),
                    '2.0' => array(
                        'frontend_input_renderer',
                        'is_global',
                        'is_visible',
                        'is_searchable',
                        'is_filterable',
                        'is_comparable',
                        'is_visible_on_front',
                        'is_html_allowed_on_front',
                        'is_used_for_price_rules',
                        'is_filterable_in_search',
                        'used_in_product_listing',
                        'used_for_sort_by',
                        'apply_to',
                        'is_visible_in_advanced_search',
                        'position',
                        'is_wysiwyg_enabled',
                        'is_used_for_promo_rules',
                        'is_required_in_admin_store',
                        'is_used_in_grid',
                        'is_visible_in_grid',
                        'is_filterable_in_grid',
                        'search_weight',
                        'additional_data',
                    )
                )
            );
            
            $this->insertOnDuplicateUpdate(
                    $this->connection, 
                    $this->tableName,
                    $catalogAttributeExtensionDatas[$GLOBALS['MAGENTO_EDITION']][$GLOBALS['MAGENTO_VERSION']],
                    $catalogAttributeExtensionColumns[$GLOBALS['MAGENTO_EDITION']][$GLOBALS['MAGENTO_VERSION']]
            );
        }
    }

    /**
     * @param CatalogAttributeExtensionInterface $attribute
     */
    public function __invoke(CatalogAttributeExtensionInterface $attribute)
    {
        $this->persist($attribute);
    }
}
