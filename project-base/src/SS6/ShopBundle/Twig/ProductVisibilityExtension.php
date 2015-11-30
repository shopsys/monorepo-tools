<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductVisibilityRepository;
use Twig_SimpleFunction;

class ProductVisibilityExtension extends \Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityRepository
	 */
	private $productVisibilityRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade $pricingGroupSettingFacade
	 * @param \SS6\ShopBundle\Component\Domain\Domain $domain
	 */
	public function __construct(
		ProductVisibilityRepository $productVisibilityRepository,
		PricingGroupSettingFacade $pricingGroupSettingFacade,
		Domain $domain
	) {
		$this->productVisibilityRepository = $productVisibilityRepository;
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
		$this->domain = $domain;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('isVisibleForDefaultPricingGroup', [$this, 'isVisibleForDefaultPricingGroupOnDomain']),
			new Twig_SimpleFunction(
				'isVisibleForDefaultPricingGroupOnEachDomain', [$this, 'isVisibleForDefaultPricingGroupOnEachDomain']
			),
		];
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'product_visibility';
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @return bool
	 */
	public function isVisibleForDefaultPricingGroupOnDomain(Product $product, $domainId) {
		$pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainId);
		$productVisibility = $this->productVisibilityRepository->getProductVisibility($product, $pricingGroup, $domainId);

		return $productVisibility->isVisible();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return bool
	 */
	public function isVisibleForDefaultPricingGroupOnEachDomain(Product $product) {
		foreach ($this->domain->getAll() as $domainConfig) {
			if (!$this->isVisibleForDefaultPricingGroupOnDomain($product, $domainConfig->getId())) {
				return false;
			}
		}

		return true;
	}
}
