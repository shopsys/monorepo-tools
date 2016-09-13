<?php

namespace SS6\ShopBundle\Model\Heureka;

use Heureka\ShopCertification;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Heureka\HeurekaSetting;
use SS6\ShopBundle\Model\Heureka\HeurekaShopCertificationService;
use SS6\ShopBundle\Model\Order\Order;

class HeurekaShopCertificationFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Heureka\HeurekaSetting
	 */
	private $heurekaSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Heureka\HeurekaShopCertificationService
	 */
	private $heurekaShopCertificationService;

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Domain $domain
	 * @param \SS6\ShopBundle\Model\Heureka\HeurekaSetting
	 * @param \SS6\ShopBundle\Model\Heureka\HeurekaShopCertificationService
	 */
	public function __construct(
		Domain $domain,
		HeurekaSetting $heurekaSetting,
		HeurekaShopCertificationService $heurekaShopCertificationService
	) {
		$this->domain = $domain;
		$this->heurekaSetting = $heurekaSetting;
		$this->heurekaShopCertificationService = $heurekaShopCertificationService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return \Heureka\ShopCertification
	 */
	public function create(Order $order) {
		$domainConfig = $this->domain->getDomainConfigById($order->getDomainId());

		$languageId = $this->heurekaShopCertificationService->getLanguageIdByLocale($domainConfig->getLocale());
		$heurekaApiKey = $this->heurekaSetting->getApiKeyByDomainId($domainConfig->getId());
		$options = ['service' => $languageId];

		$heurekaShopCertification = new ShopCertification($heurekaApiKey, $options);
		$heurekaShopCertification->setOrderId($order->getId());
		$heurekaShopCertification->setEmail($order->getEmail());
		foreach ($order->getProductItems() as $item) {
			if ($item->hasProduct()) {
				$heurekaShopCertification->addProductItemId($item->getProduct()->getId());
			}
		}

		return $heurekaShopCertification;
	}

}
