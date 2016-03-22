<?php

namespace Luni\Component\MagentoDriver\Persister;

use Luni\Component\MagentoDriver\Model\SuperLinkInterface;

interface SuperLinkPersisterInterface
{
    public function initialize();

    /**
     * @param SuperLinkInterface $superLink
     */
    public function persist(SuperLinkInterface $superLink);

    /**
     * @param SuperLinkInterface $superLink
     */
    public function __invoke(SuperLinkInterface $superLink);

    public function flush();
}
