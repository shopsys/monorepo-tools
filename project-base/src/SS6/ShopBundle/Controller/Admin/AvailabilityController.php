<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AvailabilityController extends Controller {

	/**
	 * @Route("/product/availability/list/")
	 */
	public function listAction() {
		$availabilityInlineEdit = $this->get('ss6.shop.product.availability.availability_inline_edit');
		/* @var $availabilityInlineEdit \SS6\ShopBundle\Model\Product\Availability\AvailabilityInlineEdit */

		$grid = $availabilityInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Availability/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

	/**
	 * @Route("/product/availability/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$availabilityFacade = $this->get('ss6.shop.product.availability.availability_facade');
		/* @var $availabilityFacade \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade */

		try {
			$fullName = $availabilityFacade->getById($id)->getName();
			$availabilityFacade->deleteById($id);

			$flashMessageSender->addSuccessFlashTwig('Dostupnost <strong>{{ name }}</strong> byla smazána', array(
				'name' => $fullName,
			));
		} catch (\SS6\ShopBundle\Model\Product\Availability\Exception\AvailabilityNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolená dostupnost neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_availability_list'));
	}

}
