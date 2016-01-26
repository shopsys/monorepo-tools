<?php

namespace SS6\ShopBundle\Model\Heureka;

use Heureka\ShopCertification;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Order\Order;

class HeurekaShopCertificationFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return \Heureka\ShopCertification
	 */
	public function create(Order $order) {
		$domainConfig = $this->domain->getDomainConfigById($order->getDomainId());
		$languageId = $this->getLanguageIdByLocale($domainConfig->getLocale());
		$heurekaApiKey = '';
		$options = ['service' => $languageId];

		$heurekaShopCertification = new ShopCertification($heurekaApiKey, $options);
		$heurekaShopCertification->setOrderId($order->getId());
		$heurekaShopCertification->setEmail($order->getEmail());
		foreach ($order->getProductItems() as $item) {
			$heurekaShopCertification->addProductItemId($item->getProduct()->getId());
		}

		return $heurekaShopCertification;
	}

	/**
	 * @param string $locale
	 * @return int
	 */
	private function getLanguageIdByLocale($locale) {
		$supportedLanguagesByLocale = [
			'cs' => ShopCertification::HEUREKA_CZ,
			'sk' => ShopCertification::HEUREKA_SK,
		];

		if (array_key_exists($locale, $supportedLanguagesByLocale)) {
			return $supportedLanguagesByLocale[$locale];
		}

		$message = 'Locale "' . $locale . '" is not supported.';
		throw new \SS6\ShopBundle\Model\Heureka\Exception\LocaleNotSupportedException($message);
	}

}
