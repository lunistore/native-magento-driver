<?php

namespace Luni\Component\MagentoDriver\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Option implements OptionInterface
{
    /**
     * @var int
     */
    private $value;

    /**
     * @var OptionLocaleInterface
     */
    private $default;

    /**
     * @var Collection|OptionLocaleInterface[]
     */
    private $locales;

    /**
     * @param int                                $value
     * @param OptionLocaleInterface              $default
     * @param Collection|OptionLocaleInterface[] $locales
     */
    public function __construct(
        $value,
        OptionLocaleInterface $default,
        Collection $locales
    ) {
        $this->value = $value;
        $this->default = $default;

        $this->locales = new ArrayCollection();
        foreach ($locales as $locale) {
            if (!$locale instanceof OptionLocaleInterface) {
                continue;
            }

            $this->locales->set($locale->getStoreId(), $locale);
        }
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return OptionLocaleInterface
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param int $storeId
     *
     * @return mixed
     */
    public function getLocale($storeId)
    {
        return $this->locales->get($storeId);
    }

    /**
     * @param int $storeId
     *
     * @return OptionLocaleInterface|mixed
     */
    public function getLocaleOrDefault($storeId)
    {
        if ($this->locales->containsKey($storeId)) {
            return $this->locales->get($storeId);
        }

        return $this->default;
    }

    /**
     * @return null|string
     */
    public function getDefaultLabel()
    {
        $locale = $this->getDefault();
        if ($locale !== null) {
            return $locale->getLabel();
        }

        return;
    }

    /**
     * @param int $storeId
     *
     * @return null|string
     */
    public function getLocaleLabel($storeId)
    {
        $locale = $this->getLocale($storeId);
        if ($locale !== null) {
            return $locale->getLabel();
        }

        return;
    }

    /**
     * @param int $storeId
     *
     * @return null|string
     */
    public function getLocaleOrDefaultLabel($storeId)
    {
        $locale = $this->getLocaleOrDefault($storeId);
        if ($locale !== null) {
            return $locale->getLabel();
        }

        return;
    }

    /**
     * @return Collection|OptionLocaleInterface[]
     */
    public function getAllLocales()
    {
        return $this->locales;
    }
}