<?php

namespace Shopsys\FrameworkBundle\Component\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Setting\Setting;

class EntityManagerFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    public function __construct(EntityManagerInterface $em, Setting $setting)
    {
        $this->em = $em;
        $this->setting = $setting;
    }

    /**
     * This method should be called instead of EntityManager::clear()
     * because it clears entities cached in application too.
     */
    public function clear()
    {
        $this->em->clear();
        $this->setting->clearCache();
    }
}
