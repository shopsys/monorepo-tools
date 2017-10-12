<?php

namespace Tests\ShopBundle\Unit\Component\UploadedFile;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig;
use Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFile;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFileDeleteDoctrineListener;
use Shopsys\ShopBundle\Component\UploadedFile\UploadedFileFacade;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UploadedFileDeleteDoctrineListenerTest extends PHPUnit_Framework_TestCase
{
    public function testPreRemoveDeleteFile()
    {
        $uploadedFile = new UploadedFile('entityName', 1, 'dummy.txt');

        $uploadedFileConfig = new UploadedFileConfig([]);

        $uploadedFileFacadeMock = $this->getMockBuilder(UploadedFileFacade::class)
            ->setMethods(['deleteFileFromFilesystem'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileFacadeMock->expects($this->once())->method('deleteFileFromFilesystem')->with($this->equalTo($uploadedFile));

        $containerMock = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMockForAbstractClass();
        $containerMock->expects($this->once())->method('get')->willReturn($uploadedFileFacadeMock);

        $args = $this->getMockBuilder(LifecycleEventArgs::class)
            ->setMethods(['getEntity'])
            ->disableOriginalConstructor()
            ->getMock();
        $args->method('getEntity')->willReturn($uploadedFile);

        $doctrineListener = new UploadedFileDeleteDoctrineListener($containerMock, $uploadedFileConfig);
        $doctrineListener->preRemove($args);
    }

    public function testPreRemoveDeleteUploadedFile()
    {
        $entity = new Dummy();
        $uploadedFile = new UploadedFile('entitzId', 1, 'dummy.txt');

        $uploadedFileEntityConfig = new UploadedFileEntityConfig('entityName', Dummy::class);
        $uploadedFileConfig = new UploadedFileConfig([
            Dummy::class => $uploadedFileEntityConfig,
        ]);

        $uploadedFileFacadeMock = $this->getMockBuilder(UploadedFileFacade::class)
            ->setMethods(['getUploadedFileByEntity'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileFacadeMock
            ->expects($this->once())
            ->method('getUploadedFileByEntity')
            ->with($this->equalTo($entity))
            ->willReturn($uploadedFile);

        $containerMock = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMockForAbstractClass();
        $containerMock->expects($this->once())->method('get')->willReturn($uploadedFileFacadeMock);

        $emMock = $this->getMockBuilder(EntityManager::class)
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();
        $emMock->expects($this->once())->method('remove')->with($uploadedFile);

        $args = $this->getMockBuilder(LifecycleEventArgs::class)
            ->setMethods(['getEntity', 'getEntityManager'])
            ->disableOriginalConstructor()
            ->getMock();
        $args->method('getEntity')->willReturn($entity);
        $args->method('getEntityManager')->willReturn($emMock);

        $doctrineListener = new UploadedFileDeleteDoctrineListener($containerMock, $uploadedFileConfig);
        $doctrineListener->preRemove($args);
    }
}
