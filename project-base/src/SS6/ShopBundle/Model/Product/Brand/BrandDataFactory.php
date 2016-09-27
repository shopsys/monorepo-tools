<?php

namespace SS6\ShopBundle\Model\Product\Brand;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use SS6\ShopBundle\Model\Product\Brand\Brand;
use SS6\ShopBundle\Model\Product\Brand\BrandData;

class BrandDataFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(
		FriendlyUrlFacade $friendlyUrlFacade,
		Domain $domain
	) {
		$this->friendlyUrlFacade = $friendlyUrlFacade;
		$this->domain = $domain;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Brand\Brand $brand
	 * @return \SS6\ShopBundle\Model\Product\Brand\BrandData
	 */
	public function createFromBrand(Brand $brand) {
		$brandData = new BrandData();
		$brandData->setFromEntity($brand);

		foreach ($this->domain->getAll() as $domainConfig) {
			$brandData->urls->mainOnDomains[$domainConfig->getId()] =
				$this->friendlyUrlFacade->findMainFriendlyUrl(
					$domainConfig->getId(),
					'front_brand_detail',
					$brand->getId()
				);
		}

		return $brandData;
	}

}
