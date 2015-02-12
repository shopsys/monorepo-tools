<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Category\CategoryFormType;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Category\CategoryData;
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

		$category = $categoryFacade->getById($id);
		$form = $this->createForm(new CategoryFormType($categoryFacade->getAllWithoutBranch($category)));

		$categoryData = new CategoryData();

		if (!$form->isSubmitted()) {
			$categoryData->setFromEntity($category);
		}

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

		$form = $this->createForm(new CategoryFormType($categoryFacade->getAll()));

		$categoryData = new CategoryData();

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
	 */
	public function listAction() {
		$categoryFacade = $this->get('ss6.shop.category.category_facade');
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */

		return $this->render('@SS6Shop/Admin/Content/Category/list.html.twig', [
			'rootCategories' => $categoryFacade->getAllInRootEagerLoaded(),
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

}
