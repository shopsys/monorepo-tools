<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Product\Availability\AvailabilitySettingFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AvailabilityController extends Controller {

	/**
	 * @Route("/product/availability/list/")
	 */
	public function listAction() {
		$availabilityInlineEdit = $this->get('ss6.shop.product.availability.availability_inline_edit');
		/* @var $availabilityInlineEdit \SS6\ShopBundle\Model\Product\Availability\AvailabilityInlineEdit */

		$grid = $availabilityInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Availability/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
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

			$flashMessageSender->addSuccessFlashTwig('Dostupnost <strong>{{ name }}</strong> byla smazána', [
				'name' => $fullName,
			]);
		} catch (\SS6\ShopBundle\Model\Product\Availability\Exception\AvailabilityNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolená dostupnost neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_availability_list'));
	}

	/**
	 * @Route("/product/availability/setting/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function settingAction(Request $request) {
		$availabilityFacade = $this->get('ss6.shop.product.availability.availability_facade');
		/* @var $availabilityFacade \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade */
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$availabilities = $availabilityFacade->getAll();
		$form = $this->createForm(new AvailabilitySettingFormType($availabilities));

		$availabilitySettingsFormData = [];
		$availabilitySettingsFormData['defaultInStockAvailability'] = $availabilityFacade->getDefaultInStockAvailability();

		$form->setData($availabilitySettingsFormData);

		$form->handleRequest($request);

		if ($form->isValid()) {
			$availabilitySettingsFormData = $form->getData();
			$availabilityFacade->setDefaultInStockAvailability($availabilitySettingsFormData['defaultInStockAvailability']);
			$flashMessageSender->addSuccessFlash('Nastavení výchozí dostupnosti pro zboží skladem bylo upraveno');

			return $this->redirect($this->generateUrl('admin_availability_list'));
		}

		return $this->render('@SS6Shop/Admin/Content/Availability/setting.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
