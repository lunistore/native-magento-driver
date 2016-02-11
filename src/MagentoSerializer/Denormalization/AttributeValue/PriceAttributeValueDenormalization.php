<?php

namespace Luni\Component\MagentoSerializer\Denormalization\AttributeValue;

use Luni\Component\MagentoDriver\Model\AttributeValueInterface;
use Luni\Component\MagentoDriver\Model\Immutable\ImmutableDecimalAttributeValue;
use Luni\Component\MagentoDriver\Repository\AttributeRepositoryInterface;

class PriceAttributeValueDenormalization
    extends DecimalAttributeValueDenormalization
    implements DenormalizerInterface
{
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param mixed $data
     * @param string $class
     * @param null $format
     * @param array $context
     * @return AttributeValueInterface
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        return new ImmutableDecimalAttributeValue(
            $this->attributeRepository->findOneByCode('catalog_product', $data['attribute']),
            $data['value'],
            null,
            isset($data['channel']) || isset($data['locale']) ? 1 : null
        );
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param null $format
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return isset($data['attribute']) && isset($data['value']);
    }
}