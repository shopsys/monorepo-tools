<?php

namespace Shopsys\FrameworkBundle\Component\Domain;

use Shopsys\FrameworkBundle\Component\FileUpload\FileUpload;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Symfony\Component\Filesystem\Filesystem;

class DomainFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    protected $pricingSetting;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\DomainService
     */
    protected $domainService;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
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
        PricingSetting $pricingSetting,
        DomainService $domainService,
        Filesystem $fileSystem,
        FileUpload $fileUpload
    ) {
        $this->domainImagesDirectory = $domainImagesDirectory;
        $this->domain = $domain;
        $this->pricingSetting = $pricingSetting;
        $this->domainService = $domainService;
        $this->filesystem = $fileSystem;
        $this->fileUpload = $fileUpload;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig[]
     */
    public function getDomainConfigsByCurrency(Currency $currency)
    {
        $domainConfigs = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $domainCurrencyId = $this->pricingSetting->getDomainDefaultCurrencyIdByDomainId($domainConfig->getId());
            if ($domainCurrencyId === $currency->getId()) {
                $domainConfigs[] = $domainConfig;
            }
        }

        return $domainConfigs;
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
        return $this->filesystem->exists($this->domainImagesDirectory . '/' . $domainId . '.png');
    }
}
