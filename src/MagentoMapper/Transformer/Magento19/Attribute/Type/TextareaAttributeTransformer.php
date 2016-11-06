<?php
/**
 * Copyright (c) 2016 Kiboko SAS
 *
 * @author Grégory Planchat <gregory@kiboko.fr>
 */

namespace Kiboko\Component\MagentoMapper\Transformer\Magento19\Attribute\Type;

use Kiboko\Component\MagentoDriver\Model\Attribute;
use Kiboko\Component\MagentoDriver\Model\AttributeInterface as KibokoAttributeInterface;
use Kiboko\Component\MagentoMapper\Mapper\EntityTypeMapperInterface;
use Kiboko\Component\MagentoMapper\Transformer\AttributeTransformerInterface;
use Pim\Component\Catalog\Model\AttributeInterface as PimAttributeInterface;

class TextareaAttributeTransformer
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
     *
     * @return KibokoAttributeInterface[]
     */
    public function transform(PimAttributeInterface $attribute)
    {
        yield new Attribute(
            $this->entityTypeMapper->map($attribute->getEntityType()), // entity_type_id
            $attribute->getCode(),                                     // attribute_code
            null,                                                      // attribute_model
            'text',                                                    // backend_type
            null,                                                      // backend_model
            null,                                                      // backend_table
            null,                                                      // frontend_model
            'textarea',                                                // frontend_input
            $attribute->getLabel(),                                    // frontend_label
            null,                                                      // frontend_class
            null,                                                      // source_model
            $attribute->isRequired(),                                  // is_required
            true,                                                      // is_user_defined
            $attribute->isUnique(),                                    // is_unique
            null,                                                      // default_value
            null                                                       // note
        );
    }

    /**
     * @param PimAttributeInterface $attribute
     *
     * @return bool
     */
    public function supportsTransformation(PimAttributeInterface $attribute)
    {
        return $attribute->getAttributeType() === 'pim_catalog_textarea';
    }
}