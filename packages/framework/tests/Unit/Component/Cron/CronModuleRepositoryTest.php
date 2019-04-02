<?php

namespace Tests\FrameworkBundle\Unit\Component\Cron;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Cron\CronModule;
use Shopsys\FrameworkBundle\Component\Cron\CronModuleFactory;
use Shopsys\FrameworkBundle\Component\Cron\CronModuleRepository;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class CronModuleRepositoryTest extends TestCase
{
    public function testGetCronModuleReturnsCorrectInstance()
    {
        $doctrineRepositoryMock = $this->createNullDoctrineRepositoryMock();
        $em = $this->createEntityManagerMockWithRepository($doctrineRepositoryMock);

        $repository = new CronModuleRepository($em, new CronModuleFactory(new EntityNameResolver([])));
        $cronModule = $repository->getCronModuleByServiceId('serviceId');
        $this->assertInstanceOf(CronModule::class, $cronModule);
    }

    /**
     * @param \Doctrine\ORM\EntityRepository $entityRepository
     * @return \PHPUnit\Framework\MockObject\MockObject|\Doctrine\ORM\EntityManagerInterface
     */
    private function createEntityManagerMockWithRepository(EntityRepository $entityRepository)
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($entityRepository);
        return $em;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Doctrine\ORM\EntityRepository
     */
    private function createNullDoctrineRepositoryMock()
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->willReturn(null);
        return $repository;
    }
}
