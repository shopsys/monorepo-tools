<?php

namespace Shopsys\ShopBundle\Component\Doctrine;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Setting\Setting;

class EntityManagerFacade
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    public function __construct(EntityManager $em, Setting $setting) {
        $this->em = $em;
        $this->setting = $setting;
    }

    /**
     * This method should be called instead of EntityManager::clear()
     * because it clears entites cached in application too.
     */
    public function clear() {
        $this->em->clear();
        $this->setting->clearCache();
    }

}
