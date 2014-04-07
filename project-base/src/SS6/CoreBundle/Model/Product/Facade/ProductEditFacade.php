<?php

namespace SS6\CoreBundle\Model\Product\Facade;

use Doctrine\ORM\EntityManager;
use SS6\CoreBundle\Model\Product\Repository\ProductRepository;
use SS6\CoreBundle\Model\Product\Service\ProductEditService;
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
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\CoreBundle\Model\Product\Repository\ProductRepository $productRepository
	 * @param \SS6\CoreBundle\Model\Product\Service\ProductEditService $productEditService
	 */
	public function __construct(EntityManager $em, ProductRepository $productRepository,
			ProductEditService $productEditService) {
		$this->em = $em;
		$this->productRepository = $productRepository;
		$this->productEditService = $productEditService;
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

		return true;
	}
}
