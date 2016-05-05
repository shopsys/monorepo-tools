<?php

namespace SS6\ShopBundle\Component\Domain;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Domain\DomainService;
use SS6\ShopBundle\Component\FileUpload\FileUpload;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\Filesystem\Filesystem;

class DomainFacade {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\DomainService
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
	 * @var \SS6\ShopBundle\Component\FileUpload\FileUpload
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
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return \SS6\ShopBundle\Component\Domain\Config\DomainConfig[]
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
