<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DepartmentController extends Controller {

	/**
	 * @Route("/department/list/")
	 */
	public function listAction() {
		$departmentInlineEdit = $this->get('ss6.shop.department.department_inline_edit');
		/* @var $departmentInlineEdit \SS6\ShopBundle\Model\Department\DepartmentInlineEdit */

		$grid = $departmentInlineEdit->getGrid();

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

		$fullName = $departmentFacade->getById($id)->getName();

		$departmentFacade->deleteById($id);

		$flashMessageSender->addSuccessTwig('Oddělení <strong>{{ name }}</strong> bylo smazáno', array(
			'name' => $fullName,
		));

		return $this->redirect($this->generateUrl('admin_department_list'));
	}

}
