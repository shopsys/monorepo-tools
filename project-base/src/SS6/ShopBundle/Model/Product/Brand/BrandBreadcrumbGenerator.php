<?php

namespace SS6\ShopBundle\Model\Product\Brand;

use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbItem;

class BrandBreadcrumbGenerator implements BreadcrumbGeneratorInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\BrandRepository
	 */
	private $brandRepository;

	public function __construct(BrandRepository $brandRepository) {
		$this->brandRepository = $brandRepository;
	}

	/**
	 * @param string $routeName
	 * @param array $routeParameters
	 * @return \SS6\ShopBundle\Component\Breadcrumb\BreadcrumbItem[]
	 */
	public function getBreadcrumbItems($routeName, array $routeParameters = []) {

		$isBrandDetail = $routeName === 'front_brand_detail';

		$breadcrumbItems[] = new BreadcrumbItem(
			t('Přehled značek'),
			$isBrandDetail ? 'front_brand_list' : null
		);

		if ($isBrandDetail) {
			$brand = $this->brandRepository->getById($routeParameters['id']);
			$breadcrumbItems[] = new BreadcrumbItem(
				$brand->getName()
			);
		}

		return $breadcrumbItems;
	}

}
