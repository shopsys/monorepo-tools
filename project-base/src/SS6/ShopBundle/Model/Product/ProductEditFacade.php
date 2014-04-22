<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditService;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Product\ProductVisibilityRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

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
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityRepository
	 */
	private $productVisibilityRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductEditService
	 */
	private $productEditService;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 * @param \SS6\ShopBundle\Model\Product\ProductEditService $productEditService
	 */
	public function __construct(EntityManager $em, ProductRepository $productRepository,
			ProductVisibilityRepository $productVisibilityRepository,
			ProductEditService $productEditService) {
		$this->em = $em;
		$this->productRepository = $productRepository;
		$this->productVisibilityRepository = $productVisibilityRepository;
		$this->productEditService = $productEditService;
	}
	
	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param \Symfony\Component\Form\Form $form
	 * @return boolean
	 */
	public function create(Request $request, Form $form) {
		$product = new Product();
		$form->setData($product);
		$form->handleRequest($request);
		
		if (!$form->isSubmitted()) {
			return false;
		}
		
		$this->productEditService->edit($product);

		$this->em->persist($product);
		$this->em->flush();
		
		$this->productVisibilityRepository->refreshProductsVisibility();

		return true;
	}
	
	/**
	 * @param int $productId
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param \Symfony\Component\Form\Form $form
	 * @return boolean
	 */
	public function edit($productId, Request $request, Form $form) {
		$product = $this->productRepository->getById($productId);
		$form->setData($product);
		$form->handleRequest($request);
		
		if (!$form->isSubmitted()) {
			return false;
		}
		
		$this->productEditService->edit($product);

		$this->em->persist($product);
		$this->em->flush();
		
		$this->productVisibilityRepository->refreshProductsVisibility();

		return true;
	}
	
	/**
	 * @param int $productId
	 */
	public function delete($productId) {
		$product = $this->productRepository->getById($productId);
		$this->em->remove($product);
		$this->em->flush();
	}
}
