<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Form\Admin\Product\Availability\AvailabilitySettingFormType;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityInlineEdit;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AvailabilityController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
	 */
	private $confirmDeleteResponseFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade
	 */
	private $availabilityFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\AvailabilityInlineEdit
	 */
	private $availabilityInlineEdit;

	public function __construct(
		AvailabilityFacade $availabilityFacade,
		AvailabilityInlineEdit $availabilityInlineEdit,
		ConfirmDeleteResponseFactory $confirmDeleteResponseFactory
	) {
		$this->availabilityFacade = $availabilityFacade;
		$this->availabilityInlineEdit = $availabilityInlineEdit;
		$this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
	}

	/**
	 * @Route("/product/availability/list/")
	 */
	public function listAction() {
		$grid = $this->availabilityInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Availability/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/product/availability/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function deleteAction(Request $request, $id) {
		$newId = $request->get('newId');

		try {
			$fullName = $this->availabilityFacade->getById($id)->getName();

			$this->availabilityFacade->deleteById($id, $newId);

			if ($newId === null) {
				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Availability <strong>{{ name }}</strong> deleted'),
					[
						'name' => $fullName,
					]
				);
			} else {
				$newAvailability = $this->availabilityFacade->getById($newId);
				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Availability <strong>{{ oldName }}</strong> deleted and replaced by availability <strong>{{ newName }}</strong>'),
					[
						'oldName' => $fullName,
						'newName' => $newAvailability->getName(),
					]
				);
			}

		} catch (\SS6\ShopBundle\Model\Product\Availability\Exception\AvailabilityNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Selected availatibily doesn\'t exist.'));
		}

		return $this->redirectToRoute('admin_availability_list');
	}

	/**
	 * @Route("/product/availability/delete-confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		try {
			$availability = $this->availabilityFacade->getById($id);
			$isAvailabilityDefault = $this->availabilityFacade->isAvailabilityDefault($availability);
			if ($this->availabilityFacade->isAvailabilityUsed($availability) || $isAvailabilityDefault) {
				if ($isAvailabilityDefault) {
					$message = t(
						'Availability "%name%" set as default. For deleting it you have to choose other one to be set everywhere where the existing one is used. Which availability you want to set instead?',
						['%name%' => $availability->getName()]
					);
				} else {
					$message = t(
						'Because availability "%name%"  is used with other products also, you have to choose a new availability which will replace the existing one. Which availability you want to set to these products?',
						['%name%' => $availability->getName()]
					);
				}
				$remainingAvailabilitiesList = new ObjectChoiceList($this->availabilityFacade->getAllExceptId($id), 'name', [], null, 'id');

				return $this->confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
					$message,
					'admin_availability_delete',
					$id,
					$remainingAvailabilitiesList
				);
			} else {
				$message = t(
					'Do you really want to remove availability "%name%" permanently? It is not used anywhere.',
					['%name%' => $availability->getName()]
				);

				return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_availability_delete', $id);
			}
		} catch (\SS6\ShopBundle\Model\Product\Availability\Exception\AvailabilityNotFoundException $ex) {
			return new Response(t('Selected availability doesn\'t exist'));
		}
	}

	/**
	 * @Route("/product/availability/setting/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function settingAction(Request $request) {
		$availabilities = $this->availabilityFacade->getAll();
		$form = $this->createForm(new AvailabilitySettingFormType($availabilities));

		$availabilitySettingsFormData = [];
		$availabilitySettingsFormData['defaultInStockAvailability'] = $this->availabilityFacade->getDefaultInStockAvailability();

		$form->setData($availabilitySettingsFormData);

		$form->handleRequest($request);

		if ($form->isValid()) {
			$availabilitySettingsFormData = $form->getData();

			$this->availabilityFacade->setDefaultInStockAvailability($availabilitySettingsFormData['defaultInStockAvailability']);

			$this->getFlashMessageSender()->addSuccessFlash(t('Default availability for the stock settings modified'));

			return $this->redirectToRoute('admin_availability_list');
		}

		return $this->render('@SS6Shop/Admin/Content/Availability/setting.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
