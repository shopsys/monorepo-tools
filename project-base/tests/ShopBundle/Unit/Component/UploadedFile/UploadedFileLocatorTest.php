<?php

namespace Tests\ShopBundle\Unit\Component\UploadedFile;

use PHPUnit_Framework_TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile;
use Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFileLocator;

class UploadedFileLocatorTest extends PHPUnit_Framework_TestCase
{
    public function testFileExists()
    {
        $uploadedFileDir = __DIR__ . '/UploadedFileLocatorData/';
        $uploadedFileUrlPrefix = '';

        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['getFilename', 'getEntityName'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileMock->method('getFilename')->willReturn('dummy.txt');
        $uploadedFileMock->method('getEntityName')->willReturn('entityName');

        $uploadedFileLocator = new UploadedFileLocator($uploadedFileDir, $uploadedFileUrlPrefix);
        $this->assertTrue($uploadedFileLocator->fileExists($uploadedFileMock));
    }

    public function testFileNotExists()
    {
        $uploadedFileDir = __DIR__ . '/UploadedFileLocatorData';
        $uploadedFileUrlPrefix = '';

        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['getFilename', 'getEntityName'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileMock->method('getFilename')->willReturn('non-existent.txt');
        $uploadedFileMock->method('getEntityName')->willReturn('entityName');

        $uploadedFileLocator = new UploadedFileLocator($uploadedFileDir, $uploadedFileUrlPrefix);
        $this->assertFalse($uploadedFileLocator->fileExists($uploadedFileMock));
    }

    public function testGetAbsoluteFilePath()
    {
        $uploadedFileDir = __DIR__ . '/UploadedFileLocatorData/';
        $uploadedFileUrlPrefix = '';

        $uploadedFileLocator = new UploadedFileLocator($uploadedFileDir, $uploadedFileUrlPrefix);
        $this->assertSame(
            $uploadedFileDir . 'entityName',
            $uploadedFileLocator->getAbsoluteFilePath('entityName')
        );
    }

    public function testGetAbsoluteUploadedFileFilepath()
    {
        $uploadedFileDir = __DIR__ . '/UploadedFileLocatorData/';
        $uploadedFileUrlPrefix = '';

        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['getFilename', 'getEntityName'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileMock->method('getFilename')->willReturn('dummy.txt');
        $uploadedFileMock->method('getEntityName')->willReturn('entityName');

        $uploadedFileLocator = new UploadedFileLocator($uploadedFileDir, $uploadedFileUrlPrefix);
        $this->assertSame(
            $uploadedFileDir . 'entityName/dummy.txt',
            $uploadedFileLocator->getAbsoluteUploadedFileFilepath($uploadedFileMock)
        );
    }

    public function testGetRelativeUploadedFileFilepath()
    {
        $uploadedFileDir = __DIR__ . '/UploadedFileLocatorData';
        $uploadedFileUrlPrefix = '';

        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['getFilename', 'getEntityName'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileMock->method('getFilename')->willReturn('dummy.txt');
        $uploadedFileMock->method('getEntityName')->willReturn('entityName');

        $uploadedFileLocator = new UploadedFileLocator($uploadedFileDir, $uploadedFileUrlPrefix);
        $this->assertSame(
            'entityName/dummy.txt',
            $uploadedFileLocator->getRelativeUploadedFileFilepath($uploadedFileMock)
        );
    }

    public function testGetUploadedFileUrl()
    {
        $uploadedFileDir = __DIR__ . '/UploadedFileLocatorData/';
        $uploadedFileUrlPrefix = '/assets/';

        $domainConfig = new DomainConfig(1, 'http://www.example.com', 'example domain', 'en');

        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['getFilename', 'getEntityName'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileMock->method('getFilename')->willReturn('dummy.txt');
        $uploadedFileMock->method('getEntityName')->willReturn('entityName');

        $uploadedFileLocator = new UploadedFileLocator($uploadedFileDir, $uploadedFileUrlPrefix);
        $this->assertSame(
            'http://www.example.com/assets/entityName/dummy.txt',
            $uploadedFileLocator->getUploadedFileUrl($domainConfig, $uploadedFileMock)
        );
    }
}
