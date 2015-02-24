<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_SimpleFunction;

class SeoExtension extends \Twig_Extension {

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Seo\SeoSettingFacade
	 */
	private $seoSettingFacade;

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function __construct(
		ContainerInterface $container,
		ProductRepository $productRepository,
		SeoSettingFacade $seoSettingFacade
	) {
		$this->container = $container;
		$this->productRepository = $productRepository;
		$this->seoSettingFacade = $seoSettingFacade;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('getSeoTitleAddOn', [$this, 'getSeoTitleAddOn']),
			new Twig_SimpleFunction('getSeoMetaDescriptionGlobal', [$this, 'getSeoMetaDescriptionGlobalByDomainId']),
			new Twig_SimpleFunction('getSeoMetaDescriptionByProduct', [$this, 'getSeoMetaDescriptionByProduct']),
		];
	}

	/**
	 * @return \SS6\ShopBundle\Model\Domain\Domain
	 */
	private function getDomain() {
		// Twig extensions are loaded during assetic:dump command,
		// so they cannot be dependent on Domain service
		return $this->container->get('ss6.shop.domain');
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'seo';
	}

	/**
	 * @return string
	 */
	public function getSeoTitleAddOn() {
		$currentDomainId = $this->getDomain()->getId();
		return $this->seoSettingFacade->getTitleAddOn($currentDomainId);
	}

	/**
	 * @param int $domainId
	 * @return string
	 */
	public function getSeoMetaDescriptionGlobalByDomainId($domainId) {
		return $this->seoSettingFacade->getDescriptionMainPage($domainId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return string
	 */
	public function getSeoMetaDescriptionByProduct(Product $product) {
		$currentDomainId = $this->getDomain()->getId();
		$productDetail = $this->productRepository->findProductDomainByProductAndDomainId($product, $currentDomainId);
		$seoMetaDescription = $productDetail->getSeoMetaDescription();
		if ($seoMetaDescription === null) {
			return $this->seoSettingFacade->getDescriptionMainPage($currentDomainId);
		} else {
			return $seoMetaDescription;
		}
	}

}
