<?php

namespace Tests\ShopBundle\Unit\Component\UploadedFile\Config;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileConfig;
use Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileEntityConfig;
use Tests\ShopBundle\Unit\Component\UploadedFile\Dummy;

class UploadedFileConfigTest extends PHPUnit_Framework_TestCase
{
    public function testGetEntityName()
    {
        $entity = new Dummy();
        $fileEntityConfigsByClass = [
            Dummy::class => new UploadedFileEntityConfig('entityName', Dummy::class),
        ];
        $uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

        $this->assertSame('entityName', $uploadedFileConfig->getEntityName($entity));
    }

    public function testGetEntityNameNotFoundException()
    {
        $entity = new Dummy();
        $fileEntityConfigsByClass = [];
        $uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

        $this->setExpectedException(
            \Shopsys\ShopBundle\Component\UploadedFile\Config\Exception\UploadedFileEntityConfigNotFoundException::class
        );
        $uploadedFileConfig->getEntityName($entity);
    }

    public function testGetAllUploadedFileEntityConfigs()
    {
        $fileEntityConfigsByClass = [
            Dummy::class => new UploadedFileEntityConfig('entityName', Dummy::class),
        ];
        $uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

        $this->assertSame($fileEntityConfigsByClass, $uploadedFileConfig->getAllUploadedFileEntityConfigs());
    }

    public function testGetUploadedFileEntityConfig()
    {
        $entity = new Dummy();
        $fileEntityConfigsByClass = [];
        $uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

        $this->setExpectedException(
            \Shopsys\ShopBundle\Component\UploadedFile\Config\Exception\UploadedFileEntityConfigNotFoundException::class
        );
        $uploadedFileConfig->getUploadedFileEntityConfig($entity);
    }

    public function testHasUploadedFileEntityConfig()
    {
        $entity = new Dummy();
        $fileEntityConfigsByClass = [
            Dummy::class => new UploadedFileEntityConfig('entityName', Dummy::class),
        ];
        $uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

        $this->assertTrue($uploadedFileConfig->hasUploadedFileEntityConfig($entity));
    }

    public function testHasNotUploadedFileEntityConfig()
    {
        $entity = new Dummy();
        $fileEntityConfigsByClass = [];
        $uploadedFileConfig = new UploadedFileConfig($fileEntityConfigsByClass);

        $this->assertFalse($uploadedFileConfig->hasUploadedFileEntityConfig($entity));
    }
}
