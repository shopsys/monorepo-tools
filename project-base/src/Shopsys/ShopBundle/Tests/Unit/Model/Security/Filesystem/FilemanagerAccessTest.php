<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Security\Filesystem;

use FM\ElfinderBundle\Configuration\ElFinderConfigurationReader;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Filesystem\FilepathComparator;
use Shopsys\ShopBundle\Model\Security\Filesystem\FilemanagerAccess;

class FilemanagerAccessTest extends PHPUnit_Framework_TestCase
{
    public function isPathAccessibleProvider() {
        return [
            [
                __DIR__,
                __DIR__,
                'read',
                null,
            ],
            [
                __DIR__,
                __DIR__ . '/foo',
                'read',
                null,
            ],
            [
                __DIR__,
                __DIR__ . 'foo',
                'read',
                false,
            ],
            [
                __DIR__,
                __DIR__ . '/.foo',
                'read',
                false,
            ],
            [
                __DIR__ . '/sandbox',
                __DIR__ . '/sandboxSecreet/dummyFile',
                'read',
                false,
            ],
            [
                __DIR__ . '/sandbox',
                __DIR__ . '/sandbox/subdirectory/dummyFile',
                'read',
                null,
            ],
            [
                __DIR__ . '/sandbox',
                __DIR__ . '/sandbox/dummyFile',
                'read',
                null,
            ],
        ];
    }

    /**
     * @dataProvider isPathAccessibleProvider
     */
    public function testIsPathAccessible($fileuploadDir, $testPath, $attr, $isAccessible) {
        $elFinderConfigurationReaderMock = $this->getMock(ElFinderConfigurationReader::class, null, [], '', false);
        $filemanagerAccess = new FilemanagerAccess(
            $fileuploadDir,
            $elFinderConfigurationReaderMock,
            new FilepathComparator()
        );

        $this->assertSame($filemanagerAccess->isPathAccessible($attr, $testPath, null, null), $isAccessible);
    }

    /**
     * @dataProvider isPathAccessibleProvider
     */
    public function testIsPathAccessibleStatic($fileuploadDir, $testPath, $attr, $isAccessible) {
        $elFinderConfigurationReaderMock = $this->getMock(ElFinderConfigurationReader::class, null, [], '', false);
        $filemanagerAccess = new FilemanagerAccess(
            $fileuploadDir,
            $elFinderConfigurationReaderMock,
            new FilepathComparator()
        );
        FilemanagerAccess::injectSelf($filemanagerAccess);

        $this->assertSame(FilemanagerAccess::isPathAccessibleStatic($attr, $testPath, null, null), $isAccessible);
    }

    public function testIsPathAccessibleStaticException() {
        FilemanagerAccess::detachSelf();
        $this->setExpectedException(\Shopsys\ShopBundle\Model\Security\Filesystem\Exception\InstanceNotInjectedException::class);
        FilemanagerAccess::isPathAccessibleStatic('read', __DIR__, null, null);
    }
}
