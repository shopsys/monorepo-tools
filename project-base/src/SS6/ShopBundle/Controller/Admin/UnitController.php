<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Form\Admin\Product\Unit\UnitSettingFormType;
use SS6\ShopBundle\Model\Product\Unit\UnitFacade;
use SS6\ShopBundle\Model\Product\Unit\UnitInlineEdit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UnitController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\UnitFacade
	 */
	private $unitFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\UnitInlineEdit
	 */
	private $unitInlineEdit;

	/**
	 * @var \SS6\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
	 */
	private $confirmDeleteResponseFactory;

	public function __construct(
		UnitFacade $unitFacade,
		UnitInlineEdit $unitInlineEdit,
		ConfirmDeleteResponseFactory $confirmDeleteResponseFactory
	) {
		$this->unitFacade = $unitFacade;
		$this->unitInlineEdit = $unitInlineEdit;
		$this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
	}

	/**
	 * @Route("/product/unit/list/")
	 */
	public function listAction() {
		$unitInlineEdit = $this->unitInlineEdit;

		$grid = $unitInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Unit/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/unit/delete-confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		try {
			$unit = $this->unitFacade->getById($id);
			$isUnitDefault = $this->unitFacade->isUnitDefault($unit);

			if ($this->unitFacade->isUnitUsed($unit) || $isUnitDefault) {
				if ($isUnitDefault) {
					$message = t(
						'Jednotka "%name%" je nastavena jako výchozí. '
						. 'Pro její odstranění musíte zvolit novou výchozí jednotku.' . "\n\n"
						. 'Jakou jednotku místo ní chcete nastavit?',
						['%name%' => $unit->getName()]
					);
				} else {
					$message = t(
						'Pro odstranění jednotky "%name% musíte zvolit, která se má všude, '
						. 'kde je aktuálně používaná nastavit. Jakou jednotku místo ní chcete nastavit?',
						['%name%' => $unit->getName()]
					);
				}
					$unitNamesById = $this->unitFacade->getUnitNamesByIdExceptId($id);

					return $this->confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
						$message, 'admin_unit_delete', $id, $unitNamesById
					);
			} else {
				$message = t(
					'Opravdu si přejete trvale odstranit jednotku "%name%"? Nikde není použita.',
					['%name%' => $unit->getName()]
				);

				return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_unit_delete', $id);
			}
		} catch (\SS6\ShopBundle\Model\Product\Unit\Exception\UnitNotFoundException $ex) {
			return new Response(t('Zvolená jednotka neexistuje'));
		}
	}

	/**
	 * @Route("/product/unit/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function deleteAction(Request $request, $id) {
		$newId = $request->get('newId');

		try {
			$fullName = $this->unitFacade->getById($id)->getName();
			$this->transactional(
				function () use ($id, $newId) {
					$this->unitFacade->deleteById($id, $newId);
				}
			);

			if ($newId === null) {
				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Jednotka <strong>{{ name }}</strong> byla smazána'),
					[
						'name' => $fullName,
					]
				);
			} else {
				$newUnit = $this->unitFacade->getById($newId);
				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Jednotka <strong>{{ name }}</strong> byla smazána a byla nahrazena jednotkou <strong>{{ newName }}</strong>.'),
					[
						'name' => $fullName,
						'newName' => $newUnit->getName(),
					]
				);
			}
		} catch (\SS6\ShopBundle\Model\Product\Unit\Exception\UnitNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Zvolená jednotka neexistuje.'));
		}

		return $this->redirectToRoute('admin_unit_list');
	}

	/**
	 * @Route("/product/unit/setting/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function settingAction(Request $request) {
		$units = $this->unitFacade->getAll();
		$form = $this->createForm(new UnitSettingFormType($units));

		$unitSettingsFormData = [];
		$unitSettingsFormData['defaultUnit'] = $this->unitFacade->getDefaultUnit();

		$form->setData($unitSettingsFormData);

		$form->handleRequest($request);

		if ($form->isValid()) {
			$unitSettingsFormData = $form->getData();
			$this->transactional(
				function () use ($unitSettingsFormData) {
					$this->unitFacade->setDefaultUnit($unitSettingsFormData['defaultUnit']);
				}
			);
			$this->getFlashMessageSender()->addSuccessFlash(t('Nastavení výchozí jednotky bylo upraveno'));

			return $this->redirectToRoute('admin_unit_list');
		}

		return $this->render('@SS6Shop/Admin/Content/Unit/setting.html.twig', [
			'form' => $form->createView(),
		]);
	}
}
