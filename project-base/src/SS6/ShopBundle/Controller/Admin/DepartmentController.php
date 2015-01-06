<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Department\DepartmentFormType;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Department\DepartmentData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DepartmentController extends Controller {

	/**
	 * @Route("/department/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$departmentFacade = $this->get('ss6.shop.department.department_facade');
		/* @var $departmentFacade \SS6\ShopBundle\Model\Department\DepartmentFacade */

		$department = $departmentFacade->getById($id);
		$form = $this->createForm(new DepartmentFormType($departmentFacade->getAllWithoutBranch($department)));

		$departmentData = new DepartmentData();

		if (!$form->isSubmitted()) {
			$departmentData->setFromEntity($department);
		}

		$form->setData($departmentData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$departmentFacade->edit($id, $departmentData);

			$flashMessageSender->addSuccessFlashTwig('Byla upravena kategorie <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $department->getName(),
				'url' => $this->generateUrl('admin_department_edit', array('id' => $department->getId())),
			));
			return $this->redirect($this->generateUrl('admin_department_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem('Editace kategorie - ' . $department->getName()));

		return $this->render('@SS6Shop/Admin/Content/Department/edit.html.twig', array(
			'form' => $form->createView(),
			'department' => $department,
		));
	}

	/**
	 * @Route("/department/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$departmentFacade = $this->get('ss6.shop.department.department_facade');
		/* @var $departmentFacade \SS6\ShopBundle\Model\Department\DepartmentFacade */

		$form = $this->createForm(new DepartmentFormType($departmentFacade->getAll()));

		$departmentData = new DepartmentData();

		$form->setData($departmentData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$departmentData = $form->getData();

			$department = $departmentFacade->create($departmentData);

			$flashMessageSender->addSuccessFlashTwig('Byla vytvořena kategorie <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $department->getName(),
				'url' => $this->generateUrl('admin_department_edit', array('id' => $department->getId())),
			));
			return $this->redirect($this->generateUrl('admin_department_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Department/new.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @Route("/department/list/")
	 */
	public function listAction() {
		$departmentGridFactory = $this->get('ss6.shop.department.department_grid_factory');
		/* @var $departmentGridFactory \SS6\ShopBundle\Model\Department\Grid\DepartmentGridFactory */

		$grid = $departmentGridFactory->create();

		return $this->render('@SS6Shop/Admin/Content/Department/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

	/**
	 * @Route("/department/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$departmentFacade = $this->get('ss6.shop.department.department_facade');
		/* @var $departmentFacade \SS6\ShopBundle\Model\Department\DepartmentFacade */

		try {
			$fullName = $departmentFacade->getById($id)->getName();

			$departmentFacade->deleteById($id);

			$flashMessageSender->addSuccessFlashTwig('Kategorie <strong>{{ name }}</strong> byla smazána', array(
				'name' => $fullName,
			));
		} catch (\SS6\ShopBundle\Model\Department\Exception\DepartmentNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolená kategorie neexistuje');
		}

		return $this->redirect($this->generateUrl('admin_department_list'));
	}

}
