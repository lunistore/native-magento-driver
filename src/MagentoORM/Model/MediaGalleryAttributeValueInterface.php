<?php
/**
 * Copyright (c) 2016 Kiboko SAS.
 *
 * @author Grégory Planchat <gregory@kiboko.fr>
 */

namespace Kiboko\Component\MagentoORM\Model;

interface MediaGalleryAttributeValueInterface extends AttributeValueInterface, MappableInterface, \Countable, \IteratorAggregate
{
    public function addMedia(ImageAttributeValueInterface $attributeValue);
}
