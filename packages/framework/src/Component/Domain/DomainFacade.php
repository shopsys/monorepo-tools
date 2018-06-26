<?php

namespace Shopsys\FrameworkBundle\Component\Domain;

use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;

class DomainFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\DomainService
     */
    protected $domainService;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $domainImagesDirectory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\FileUpload
     */
    protected $fileUpload;

    public function __construct(
        $domainImagesDirectory,
        Domain $domain,
        DomainService $domainService,
        FilesystemInterface $fileSystem,
        FileUpload $fileUpload
    ) {
        $this->domainImagesDirectory = $domainImagesDirectory;
        $this->domain = $domain;
        $this->domainService = $domainService;
        $this->filesystem = $fileSystem;
        $this->fileUpload = $fileUpload;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[]
     */
    public function getAllDomainConfigs()
    {
        return $this->domain->getAll();
    }

    /**
     * @param int $domainId
     * @param string $iconName
     */
    public function editIcon($domainId, $iconName)
    {
        $temporaryFilepath = $this->fileUpload->getTemporaryFilepath($iconName);
        $this->domainService->convertToDomainIconFormatAndSave($domainId, $temporaryFilepath, $this->domainImagesDirectory);
    }

    /**
     * @param int $domainId
     * @return bool
     */
    public function existsDomainIcon($domainId)
    {
        return $this->filesystem->has($this->domainImagesDirectory . '/' . $domainId . '.png');
    }
}
