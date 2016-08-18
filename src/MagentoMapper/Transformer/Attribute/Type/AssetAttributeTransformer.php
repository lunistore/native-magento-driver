<?php

namespace Kiboko\Component\MagentoMapper\Transformer\Attribute\Type;

use Kiboko\Component\MagentoDriver\Model\Attribute;
use Kiboko\Component\MagentoDriver\Model\AttributeInterface as KibokoAttributeInterface;
use Kiboko\Component\MagentoMapper\Mapper\EntityTypeMapperInterface;
use Kiboko\Component\MagentoMapper\Transformer\AttributeTransformerInterface;
use Pim\Component\Catalog\Model\AttributeInterface as PimAttributeInterface;

class AssetAttributeTransformer
    implements AttributeTransformerInterface
{
    /**
     * @var EntityTypeMapperInterface
     */
    private $entityTypeMapper;

    /**
     * @param EntityTypeMapperInterface $entityTypeMapper
     */
    public function __construct(
        EntityTypeMapperInterface $entityTypeMapper
    ) {
        $this->entityTypeMapper = $entityTypeMapper;
    }

    /**
     * @param PimAttributeInterface $attribute
     * @param int|null              $mappedId
     *
     * @return KibokoAttributeInterface[]
     */
    public function transform(PimAttributeInterface $attribute, $mappedId = null)
    {
        return [
            Attribute::buildNewWith(
                $mappedId,                                      // attribute_id
                $this->entityTypeMapper->map($attribute),       // entity_type_id
                $attribute->getCode(),                          // attribute_code
                null,                                           // attribute_model
                'varchar',                                      // backend_type
                null,                                           // backend_model
                null,                                           // backend_table
                'catalog/product_attribute_frontend_image',     // frontend_model
                'media_image',                                  // frontend_input
                $attribute->getLabel(),                         // frontend_label
                null,                                           // frontend_class
                null,                                           // source_model
                $attribute->isRequired(),                       // is_required
                false,                                          // is_user_defined
                $attribute->isUnique(),                         // is_unique
                null,                                           // default_value
                null                                            // note
            )
        ];
    }

    /**
     * @param PimAttributeInterface $attribute
     *
     * @return bool
     */
    public function supportsTransformation(PimAttributeInterface $attribute)
    {
        return $attribute->getAttributeType() === 'pim_catalog_image';
    }
}