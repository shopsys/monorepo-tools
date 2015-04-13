<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Setting\Setting;
use Twig_SimpleFunction;

class ProductVisibilityExtension extends \Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupRepository
	 */
	private $pricingGroupRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupRepository
	 * @param \SS6\ShopBundle\Model\Setting\Setting $setting
	 */
	public function __construct(
		ProductRepository $productRepository,
		PricingGroupRepository $pricingGroupRepository,
		Setting $setting
	) {
		$this->productRepository = $productRepository;
		$this->pricingGroupRepository = $pricingGroupRepository;
		$this->setting = $setting;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('isVisibileForDefaultPricingGroup', [$this, 'isVisibileForDefaultPricingGroupOnDomain']),
		];
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'product_visibility';
	}

	/**
	 *
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @return bool
	 */
	public function isVisibileForDefaultPricingGroupOnDomain(Product $product, $domainId) {
		$pricingGroup = $this->pricingGroupRepository->getById($this->setting->get(Setting::DEFAULT_PRICING_GROUP, $domainId));
		$productVisibility = $this->productRepository->findProductVisibility($product, $pricingGroup, $domainId);

		if ($productVisibility !== null) {
			return $productVisibility->isVisible();
		}

		return false;
	}

}
