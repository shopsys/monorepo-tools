<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\Admin\Category\CategoryFormTypeFactory;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Category\CategoryDataFactory;
use SS6\ShopBundle\Model\Category\CategoryFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CategoryController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryDataFactory
	 */
	private $categoryDataFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Category\CategoryFormTypeFactory
	 */
	private $categoryFormTypeFactory;

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $session;

	/**
	 * @var \Symfony\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(
		Translator $translator,
		CategoryFacade $categoryFacade,
		CategoryFormTypeFactory $categoryFormTypeFactory,
		CategoryDataFactory $categoryDataFactory,
		Session $session,
		Domain $domain,
		Breadcrumb $breadcrumb
	) {
		$this->translator = $translator;
		$this->categoryFacade = $categoryFacade;
		$this->categoryFormTypeFactory = $categoryFormTypeFactory;
		$this->categoryDataFactory = $categoryDataFactory;
		$this->session = $session;
		$this->domain = $domain;
		$this->breadcrumb = $breadcrumb;
	}

	/**
	 * @Route("/category/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function editAction(Request $request, $id) {
		$category = $this->categoryFacade->getById($id);
		$form = $this->createForm($this->categoryFormTypeFactory->createForCategory($category));

		$categoryData = $this->categoryDataFactory->createFromCategory($category);

		$form->setData($categoryData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->transactional(
				function () use ($id, $categoryData) {
					$this->categoryFacade->edit($id, $categoryData);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				'Byla upravena kategorie <strong><a href="{{ url }}">{{ name }}</a></strong>',
				[
					'name' => $category->getName(),
					'url' => $this->generateUrl('admin_category_edit', ['id' => $category->getId()]),
				]
			);
			return $this->redirectToRoute('admin_category_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$this->breadcrumb->replaceLastItem(new MenuItem($this->translator->trans('Editace kategorie - ') . $category->getName()));

		return $this->render('@SS6Shop/Admin/Content/Category/edit.html.twig', [
			'form' => $form->createView(),
			'category' => $category,
		]);
	}

	/**
	 * @Route("/category/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$form = $this->createForm($this->categoryFormTypeFactory->create());

		$categoryData = $this->categoryDataFactory->createDefault();

		$form->setData($categoryData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$categoryData = $form->getData();

			$category = $this->transactional(
				function () use ($categoryData) {
					return $this->categoryFacade->create($categoryData);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig(
				'Byla vytvořena kategorie <strong><a href="{{ url }}">{{ name }}</a></strong>',
				[
					'name' => $category->getName(),
					'url' => $this->generateUrl('admin_category_edit', ['id' => $category->getId()]),
				]
			);

			return $this->redirectToRoute('admin_category_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Category/new.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/category/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction(Request $request) {
		if ($request->query->has('domain')) {
			$domainId = (int)$request->query->get('domain');
		} else {
			$domainId = (int)$this->session->get('categories_selected_domain_id', 0);
		}

		if ($domainId !== 0) {
			try {
				$this->domain->getDomainConfigById($domainId);
			} catch (\SS6\ShopBundle\Component\Domain\Exception\InvalidDomainIdException $ex) {
				$domainId = 0;
			}
		}

		$this->session->set('categories_selected_domain_id', $domainId);

		if ($domainId === 0) {
			$categoryDetails = $this->categoryFacade->getAllCategoryDetails($request->getLocale());
		} else {
			$categoryDetails = $this->categoryFacade->getVisibleCategoryDetailsForDomain($domainId, $request->getLocale());
		}

		return $this->render('@SS6Shop/Admin/Content/Category/list.html.twig', [
			'categoryDetails' => $categoryDetails,
			'isForAllDomains' => ($domainId === 0),
		]);
	}

	/**
	 * @Route("/category/save-order/", methods={"post"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function saveOrderAction(Request $request) {
		$categoriesOrderingData = $request->get('categoriesOrderingData');
		foreach ($categoriesOrderingData as $categoryOrderingData) {
			$categoryId = (int)$categoryOrderingData['categoryId'];
			$parentId = $categoryOrderingData['parentId'] === '' ? null : (int)$categoryOrderingData['parentId'];
			$parentIdByCategoryId[$categoryId] = $parentId;
		}

		$this->transactional(
			function () use ($parentIdByCategoryId) {
				$this->categoryFacade->editOrdering($parentIdByCategoryId);
			}
		);

		return new Response('OK - dummy');
	}

	/**
	 * @Route("/category/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$fullName = $this->categoryFacade->getById($id)->getName();

			$this->transactional(
				function () use ($id) {
					$this->categoryFacade->deleteById($id);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Kategorie <strong>{{ name }}</strong> byla smazána', [
				'name' => $fullName,
			]);
		} catch (\SS6\ShopBundle\Model\Category\Exception\CategoryNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolená kategorie neexistuje');
		}

		return $this->redirectToRoute('admin_category_list');
	}

	public function listDomainTabsAction() {
		$domainId = $this->session->get('categories_selected_domain_id', 0);

		return $this->render('@SS6Shop/Admin/Content/Category/domainTabs.html.twig', [
			'domainConfigs' => $this->domain->getAll(),
			'selectedDomainId' => $domainId,
		]);
	}

}
