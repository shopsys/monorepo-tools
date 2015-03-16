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
	 * @Route("/product/availability/delete/{id}/{newId}", requirements={"id" = "\d+", "newId" = "\d+"})
	 * @param int $id
	 * @param int|null $newId
	 */
	public function deleteAction($id, $newId = null) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$availabilityFacade = $this->get('ss6.shop.product.availability.availability_facade');
		/* @var $availabilityFacade \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade */

		try {
			$fullName = $availabilityFacade->getById($id)->getName();
			$availabilityFacade->deleteById($id, $newId);

			if ($newId === null) {
				$flashMessageSender->addSuccessFlashTwig('Dostupnost <strong>{{ name }}</strong> byl smazána', [
					'name' => $fullName,
				]);
			} else {
				$newAvailability = $availabilityFacade->getById($newId);
				$flashMessageSender->addSuccessFlashTwig('Dostupnost <strong>{{ oldName }}</strong> byla nahrazena dostupností'
					. ' <strong>{{ newName }}</strong> a byla smazána.',
					[
						'oldName' => $fullName,
						'newName' => $newAvailability->getName(),
					]);
			}

		} catch (\SS6\ShopBundle\Model\Product\Availability\Exception\AvailabilityNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolená dostupnost neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_availability_list'));
	}

	/**
	 * @Route("/product/availability/delete_confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		$availabilityFacade = $this->get('ss6.shop.product.availability.availability_facade');
		/* @var $availabilityFacade \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade */
		$confirmDeleteResponseFactory = $this->get('ss6.shop.confirm_delete.confirm_delete_response_factory');
		/* @var $confirmDeleteResponseFactory \SS6\ShopBundle\Model\ConfirmDelete\ConfirmDeleteResponseFactory */;

		try {
			$availability = $availabilityFacade->getById($id);
			$isAvailabilityDefault = $availabilityFacade->isAvailabilityDefault($availability);
			if ($availabilityFacade->isAvailabilityUsed($availability) || $isAvailabilityDefault) {
				if ($isAvailabilityDefault) {
					$message = 'Dostupnost "' . $availability->getName() . '" je nastavena jako výchozí. '
						. 'Pro její odstranění musíte zvolit, která se má všude, '
						. 'kde je aktuálně používaná, nastavit.' . "\n\n" . 'Jakou dostupnost místo ní chcete nastavit?';
				} else {
					$message = 'Jelikož dostupnost "' . $availability->getName() . '" je používána ještě u některých produktů, '
					. 'musíte zvolit, jaká dostupnost bude použita místo ní. Jakou dostupnost chcete těmto produktům nastavit? ';
				}
				$availabilityNamesById = [];
				foreach ($availabilityFacade->getAllExceptId($id) as $newAvailabilty) {
					$availabilityNamesById[$newAvailabilty->getId()] = $newAvailabilty->getName();
				}
				return $confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
					$message,
					'admin_availability_delete',
					$id,
					$availabilityNamesById
				);
			} else {
				$message = 'Opravdu si přejete trvale odstranit dostupnost "'
					. $availability->getName() . '"? Nikde není použitá.';
				return $confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_availability_delete', $id);
			}
		} catch (\SS6\ShopBundle\Model\Product\Availability\Exception\AvailabilityNotFoundException $ex) {
			return new Response('Zvolená dostupnost neexistuje');
		}
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
