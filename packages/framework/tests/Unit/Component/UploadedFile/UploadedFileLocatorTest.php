<?php

namespace Tests\FrameworkBundle\Unit\Component\UploadedFile;

use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFile;
use Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFileLocator;

class UploadedFileLocatorTest extends TestCase
{
    public function testFileExists()
    {
        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['getFilename', 'getEntityName'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileMock->method('getFilename')->willReturn('dummy.txt');
        $uploadedFileMock->method('getEntityName')->willReturn('entityName');

        $uploadedFileLocator = $this->createUploadedFileLocator();
        $this->assertTrue($uploadedFileLocator->fileExists($uploadedFileMock));
    }

    public function testFileNotExists()
    {
        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['getFilename', 'getEntityName'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileMock->method('getFilename')->willReturn('non-existent.txt');
        $uploadedFileMock->method('getEntityName')->willReturn('entityName');

        $uploadedFileLocator = $this->createUploadedFileLocator('', false);
        $this->assertFalse($uploadedFileLocator->fileExists($uploadedFileMock));
    }

    public function testGetAbsoluteFilePath()
    {
        $uploadedFileDir = __DIR__ . '/UploadedFileLocatorData/';
        $uploadedFileUrlPrefix = '';

        $uploadedFileLocator = $this->createUploadedFileLocator($uploadedFileUrlPrefix);
        $this->assertSame(
            $uploadedFileDir . 'entityName',
            $uploadedFileLocator->getAbsoluteFilePath('entityName')
        );
    }

    public function testGetAbsoluteUploadedFileFilepath()
    {
        $uploadedFileDir = __DIR__ . '/UploadedFileLocatorData/';
        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['getFilename', 'getEntityName'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileMock->method('getFilename')->willReturn('dummy.txt');
        $uploadedFileMock->method('getEntityName')->willReturn('entityName');

        $uploadedFileLocator = $this->createUploadedFileLocator();
        $this->assertSame(
            $uploadedFileDir . 'entityName/dummy.txt',
            $uploadedFileLocator->getAbsoluteUploadedFileFilepath($uploadedFileMock)
        );
    }

    public function testGetRelativeUploadedFileFilepath()
    {
        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['getFilename', 'getEntityName'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileMock->method('getFilename')->willReturn('dummy.txt');
        $uploadedFileMock->method('getEntityName')->willReturn('entityName');

        $uploadedFileLocator = $this->createUploadedFileLocator();
        $this->assertSame(
            'entityName/dummy.txt',
            $uploadedFileLocator->getRelativeUploadedFileFilepath($uploadedFileMock)
        );
    }

    public function testGetUploadedFileUrl()
    {
        $domainConfig = new DomainConfig(1, 'http://www.example.com', 'example domain', 'en');

        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->setMethods(['getFilename', 'getEntityName'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadedFileMock->method('getFilename')->willReturn('dummy.txt');
        $uploadedFileMock->method('getEntityName')->willReturn('entityName');

        $uploadedFileLocator = $this->createUploadedFileLocator('/assets/');
        $this->assertSame(
            'http://www.example.com/assets/entityName/dummy.txt',
            $uploadedFileLocator->getUploadedFileUrl($domainConfig, $uploadedFileMock)
        );
    }

    /**
     * @param string $uploadedFileUrlPrefix
     * @param bool $has
     * @return \Shopsys\FrameworkBundle\Component\UploadedFile\UploadedFileLocator
     */
    private function createUploadedFileLocator($uploadedFileUrlPrefix = '', $has = true)
    {
        $uploadedFileDir = __DIR__ . '/UploadedFileLocatorData/';
        $filesystemMock = $this->createMock(FilesystemInterface::class);

        $filesystemMock->method('has')->willReturn($has);
        return new UploadedFileLocator($uploadedFileDir, $uploadedFileUrlPrefix, $filesystemMock);
    }
}
