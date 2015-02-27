<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Category\CategoryDataFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller {

	/**
	 * @Route("/category/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$categoryFacade = $this->get('ss6.shop.category.category_facade');
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */
		$categoryFormTypeFactory = $this->get('ss6.shop.form.admin.category_form_type_factory');
		/* @var $categoryFormTypeFactory \SS6\ShopBundle\Form\Admin\Category\CategoryFormTypeFactory */
		$categoryDataFactory = $this->get(CategoryDataFactory::class);
		/* @var $categoryDataFactory \SS6\ShopBundle\Model\Category\CategoryDataFactory */

		$category = $categoryFacade->getById($id);
		$form = $this->createForm($categoryFormTypeFactory->createForCategory($category));

		$categoryData = $categoryDataFactory->createFromCategory($category);

		$form->setData($categoryData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$categoryFacade->edit($id, $categoryData);

			$flashMessageSender->addSuccessFlashTwig('Byla upravena kategorie <strong><a href="{{ url }}">{{ name }}</a></strong>', [
				'name' => $category->getName(),
				'url' => $this->generateUrl('admin_category_edit', ['id' => $category->getId()]),
			]);
			return $this->redirect($this->generateUrl('admin_category_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem('Editace kategorie - ' . $category->getName()));

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
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$categoryFacade = $this->get('ss6.shop.category.category_facade');
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */
		$categoryFormTypeFactory = $this->get('ss6.shop.form.admin.category_form_type_factory');
		/* @var $categoryFormTypeFactory \SS6\ShopBundle\Form\Admin\Category\CategoryFormTypeFactory */
		$categoryDataFactory = $this->get(CategoryDataFactory::class);
		/* @var $categoryDataFactory \SS6\ShopBundle\Model\Category\CategoryDataFactory */

		$form = $this->createForm($categoryFormTypeFactory->create());

		$categoryData = $categoryDataFactory->createDefault();

		$form->setData($categoryData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$categoryData = $form->getData();

			$category = $categoryFacade->create($categoryData);

			$flashMessageSender->addSuccessFlashTwig('Byla vytvořena kategorie <strong><a href="{{ url }}">{{ name }}</a></strong>', [
				'name' => $category->getName(),
				'url' => $this->generateUrl('admin_category_edit', ['id' => $category->getId()]),
			]);
			return $this->redirect($this->generateUrl('admin_category_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
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
		$session = $this->get('session');
		/* @var $session \Symfony\Component\HttpFoundation\Session\Session */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$categoryFacade = $this->get('ss6.shop.category.category_facade');
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */

		if ($request->query->has('domain')) {
			$domainId = (int)$request->query->get('domain');
		} else {
			$domainId = (int)$session->get('categories_selected_domain_id', 0);
		}

		if ($domainId !== 0) {
			try {
				$domain->getDomainConfigById($domainId);
			} catch (\SS6\ShopBundle\Model\Domain\Exception\InvalidDomainIdException $ex) {
				$domainId = 0;
			}
		}

		$session->set('categories_selected_domain_id', $domainId);

		if ($domainId === 0) {
			$categoryDetails = $categoryFacade->getAllCategoryDetails($request->getLocale());
		} else {
			$categoryDetails = $categoryFacade->getVisibleCategoryDetailsForDomain($domainId, $request->getLocale());
		}

		return $this->render('@SS6Shop/Admin/Content/Category/list.html.twig', [
			'categoryDetails' => $categoryDetails,
			'allDomains' => ($domainId === 0),
		]);
	}

	/**
	 * @Route("/category/save-order/", methods={"post"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function saveOrderAction(Request $request) {
		$categoryFacade = $this->get('ss6.shop.category.category_facade');
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */

		$categoryOrderingData = [];
		foreach ($request->get('categoriesOrderingData') as $categoryOrderingData) {
			$categoryId = (int)$categoryOrderingData['categoryId'];
			$parentId = $categoryOrderingData['parentId'] === '' ? null : (int)$categoryOrderingData['parentId'];
			$parentIdByCategoryId[$categoryId] = $parentId;
		}

		$categoryFacade->editOrdering($parentIdByCategoryId);

		return new Response('OK');
	}

	/**
	 * @Route("/category/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$categoryFacade = $this->get('ss6.shop.category.category_facade');
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */

		try {
			$fullName = $categoryFacade->getById($id)->getName();

			$categoryFacade->deleteById($id);

			$flashMessageSender->addSuccessFlashTwig('Kategorie <strong>{{ name }}</strong> byla smazána', [
				'name' => $fullName,
			]);
		} catch (\SS6\ShopBundle\Model\Category\Exception\CategoryNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolená kategorie neexistuje');
		}

		return $this->redirect($this->generateUrl('admin_category_list'));
	}

	public function listDomainTabsAction() {
		$session = $this->get('session');
		/* @var $session \Symfony\Component\HttpFoundation\Session\Session */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$localization = $this->get('ss6.shop.localization.localization');
		/* @var $localization \SS6\ShopBundle\Model\Localization\Localization */

		$domainId = $session->get('categories_selected_domain_id', 0);

		return $this->render('@SS6Shop/Admin/Content/Category/domainTabs.html.twig', [
			'domainConfigs' => $domain->getAll(),
			'selectedDomainId' => $domainId,
			'multipleLocales' => count($localization->getAllLocales()) > 1,
		]);
	}

}
