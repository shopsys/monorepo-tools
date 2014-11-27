<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DepartmentController extends Controller {

	public function menuAction() {
		$departmentFacade = $this->get('ss6.shop.department.department_facade');
		/* @var $departmentFacade \SS6\ShopBundle\Model\Department\DepartmentFacade */

		$departments = $departmentFacade->getAllWithTranslation();

		return $this->render('@SS6Shop/Front/Content/Department/menu.html.twig', array(
			'departments' => $departments,
		));
	}

}
