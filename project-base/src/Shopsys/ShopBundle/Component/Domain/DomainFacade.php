<?php

namespace Shopsys\ShopBundle\Component\Domain;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Domain\DomainService;
use Shopsys\ShopBundle\Component\FileUpload\FileUpload;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\Filesystem\Filesystem;

class DomainFacade
{

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\DomainService
     */
    private $domainService;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $domainImagesDirectory;

    /**
     * @var \Shopsys\ShopBundle\Component\FileUpload\FileUpload
     */
    private $fileUpload;

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
     * @param \Shopsys\ShopBundle\Model\Pricing\Currency\Currency $currency
     * @return \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig[]
     */
    public function getDomainConfigsByCurrency(Currency $currency) {
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
     * @param int $domainId
     * @param string $iconName
     */
    public function editIcon($domainId, $iconName) {
        $temporaryFilepath = $this->fileUpload->getTemporaryFilePath($iconName);
        $this->domainService->convertToDomainIconFormatAndSave($domainId, $temporaryFilepath, $this->domainImagesDirectory);
    }

    /**
     * @param int $domainId
     * @return bool
     */
    public function existsDomainIcon($domainId) {
        return $this->filesystem->exists($this->domainImagesDirectory . '/' . $domainId . '.png');
    }

}
