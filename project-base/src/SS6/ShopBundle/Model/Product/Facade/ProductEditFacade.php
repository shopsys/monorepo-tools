<?php

namespace SS6\ShopBundle\Model\Product\Facade;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Entity\Product;
use SS6\ShopBundle\Model\Product\Repository\ProductRepository;
use SS6\ShopBundle\Model\Product\Service\ProductEditService;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class ProductEditFacade {

	/**
	 * @var EntityManager
	 */
	private $em;
	
	/**
	 * @var ProductRepository
	 */
	private $productRepository;

	/**
	 * @var ProductEditService
	 */
	private $productEditService;

	/**
	 * @param EntityManager $em
	 * @param ProductRepository $productRepository
	 * @param ProductEditService $productEditService
	 */
	public function __construct(EntityManager $em, ProductRepository $productRepository,
			ProductEditService $productEditService) {
		$this->em = $em;
		$this->productRepository = $productRepository;
		$this->productEditService = $productEditService;
	}
	
	/**
	 * @param Request $request
	 * @param Form $form
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

		return true;
	}
	
	/**
	 * @param int $productId
	 * @param Request $request
	 * @param Form $form
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
