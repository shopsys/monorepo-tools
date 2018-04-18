<?php

namespace Shopsys\FrameworkBundle\Component\Cron;

use Doctrine\ORM\EntityManagerInterface;

class CronModuleRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getCronModuleRepository()
    {
        return $this->em->getRepository(CronModule::class);
    }

    /**
     * @param string $serviceId
     * @return \Shopsys\FrameworkBundle\Component\Cron\CronModule
     */
    public function getCronModuleByServiceId($serviceId)
    {
        $cronModule = $this->getCronModuleRepository()->find($serviceId);
        if ($cronModule === null) {
            $cronModule = new CronModule($serviceId);
            $this->em->persist($cronModule);
            $this->em->flush($cronModule);
        }

        return $cronModule;
    }

    /**
     * @return string[]
     */
    public function getAllScheduledCronModuleServiceIds()
    {
        $query = $this->em->createQuery('SELECT cm.serviceId FROM ' . CronModule::class . ' cm WHERE cm.scheduled = TRUE');

        return array_map('array_pop', $query->getScalarResult());
    }
}
