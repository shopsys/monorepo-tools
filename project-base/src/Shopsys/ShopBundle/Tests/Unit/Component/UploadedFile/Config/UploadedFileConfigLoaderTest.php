<?php

namespace Shopsys\ShopBundle\Tests\Unit\Component\UploadedFile;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\UploadedFile\Config\UploadedFileConfigLoader;
use Shopsys\ShopBundle\Tests\Unit\Component\UploadedFile\Dummy;
use Symfony\Component\Filesystem\Filesystem;

class UploadedFileConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testLoadFromYaml()
    {
        $configurationFilapath = __DIR__ . '/test_config_uploaded_files.yml';
        $filesystem = new Filesystem();

        $uploadedFileConfigLoader = new UploadedFileConfigLoader($filesystem);
        $uploadedFileEntityConfig = $uploadedFileConfigLoader->loadFromYaml($configurationFilapath);
        $uploadedFileEntityConfigs = $uploadedFileEntityConfig->getAllUploadedFileEntityConfigs();

        $this->assertCount(1, $uploadedFileEntityConfigs);
        $this->assertArrayHasKey(Dummy::class, $uploadedFileEntityConfigs);
        $uploadedFileConfig = $uploadedFileEntityConfigs[Dummy::class];
        $this->assertSame('testEntity', $uploadedFileConfig->getEntityName());
        $this->assertSame(Dummy::class, $uploadedFileConfig->getEntityClass());
    }
}
