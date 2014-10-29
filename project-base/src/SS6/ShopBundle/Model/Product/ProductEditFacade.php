<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;

class ProductEditFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityFacade
	 */
	private $productVisibilityFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	/**
	 * @var SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 */
	public function __construct(
		EntityManager $em,
		ProductRepository $productRepository,
		ProductVisibilityFacade $productVisibilityFacade,
		ParameterRepository $parameterRepository,
		Domain $domain
	) {
		$this->em = $em;
		$this->productRepository = $productRepository;
		$this->productVisibilityFacade = $productVisibilityFacade;
		$this->parameterRepository = $parameterRepository;
		$this->domain = $domain;
	}

	/**
	 * @param int $productId
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getById($productId) {
		return $this->productRepository->getById($productId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function create(ProductData $productData) {
		$product = new Product($productData);

		$this->em->persist($product);
		$this->saveParameters($product, $productData->getParameters());
		$this->createProductDomains($product, $this->domain->getAll());
		$this->refreshProductDomains($product, $productData->getShowOnDomains());

		$this->em->flush();

		$this->productVisibilityFacade->refreshProductsVisibilityDelayed();

		return $product;
	}

	/**
	 * @param int $productId
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function edit($productId, ProductData $productData) {
		$product = $this->productRepository->getById($productId);

		$product->edit($productData);
		$this->saveParameters($product, $productData->getParameters());
		$this->refreshProductDomains($product, $productData->getShowOnDomains());

		$this->em->flush();

		$this->productVisibilityFacade->refreshProductsVisibilityDelayed();

		return $product;
	}

	/**
	 * @param int $productId
	 */
	public function delete($productId) {
		$product = $this->productRepository->getById($productId);
		$this->em->remove($product);
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[] $productParameterValuesData
	 */
	private function saveParameters(Product $product, array $productParameterValuesData) {
		// Doctrine runs INSERTs before DELETEs in UnitOfWork. In case of UNIQUE constraint
		// in database, this leads in trying to insert duplicate entry.
		// That's why it's necessary to do remove and flush first.

		$oldProductParameterValues = $this->parameterRepository->findParameterValuesByProduct($product);
		foreach ($oldProductParameterValues as $oldProductParameterValue) {
			$this->em->remove($oldProductParameterValue);
		}
		$this->em->flush();

		foreach ($productParameterValuesData as $productParameterValueData) {
			$productParameterValueData->setProduct($product);
			$productParameterValue = new ProductParameterValue($productParameterValueData);
			$this->em->persist($productParameterValue);
		}
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $oldVat
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $newVat
	 */
	public function replaceOldVatWithNewVat(Vat $oldVat, Vat $newVat) {
		$products = $this->productRepository->getAllByVat($oldVat);
		foreach ($products as $product) {
			$product->changeVat($newVat);
		}
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig[] $domains
	 */
	private function createProductDomains(Product $product, array $domains) {
		foreach ($domains as $domain) {
			$productDomain = new ProductDomain($product, $domain->getId());
			$this->em->persist($productDomain);
		}
		$this->em->flush();

	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param array $showOnDomainData
	 */
	private function refreshProductDomains(Product $product, array $showOnDomainData) {
		$productDomains = $this->productRepository->getProductDomainsByProduct($product);
		foreach ($productDomains as $productDomain) {
			if (in_array($productDomain->getDomainId(), $showOnDomainData)) {
				$productDomain->setShow(true);
			} else {
				$productDomain->setShow(false);
			}
		}
		$this->em->flush();
	}

}
