<?php

namespace SS6\ShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Form\Admin\Vat\VatSettingsFormType;
use SS6\ShopBundle\Model\ConfirmDelete\ConfirmDeleteResponseFactory;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\PricingSettingFacade;
use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;
use SS6\ShopBundle\Model\Pricing\Vat\VatInlineEdit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VatController extends BaseController {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\ConfirmDelete\ConfirmDeleteResponseFactory
	 */
	private $confirmDeleteResponseFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSettingFacade
	 */
	private $pricingSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatFacade
	 */
	private $vatFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatInlineEdit
	 */
	private $vatInlineEdit;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(
		Translator $translator,
		EntityManager $em,
		VatFacade $vatFacade,
		PricingSetting $pricingSetting,
		VatInlineEdit $vatInlineEdit,
		ConfirmDeleteResponseFactory $confirmDeleteResponseFactory,
		PricingSettingFacade $pricingSettingFacade
	) {
		$this->translator = $translator;
		$this->em = $em;
		$this->vatFacade = $vatFacade;
		$this->pricingSetting = $pricingSetting;
		$this->vatInlineEdit = $vatInlineEdit;
		$this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
		$this->pricingSettingFacade = $pricingSettingFacade;
	}

	/**
	 * @Route("/vat/list/")
	 */
	public function listAction() {
		$grid = $this->vatInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Vat/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/vat/delete_confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		try {
			$vat = $this->vatFacade->getById($id);
			if ($this->vatFacade->isVatUsed($vat)) {
				$message = $this->translator->trans(
					'Pro odstranění sazby "%name% musíte zvolit, která se má všude, '
					. 'kde je aktuálně používaná nastavit. Po změně sazby DPH dojde k přepočtu cen zboží '
					. '- základní cena s DPH zůstane zachována. Jakou sazbu místo ní chcete nastavit?',
					['%name%' => $vat->getName()]
				);
				$vatNamesById = $this->getVatNamesByIdExceptId($this->vatFacade, $id);

				return $this->confirmDeleteResponseFactory->createSetNewAndDeleteResponse($message, 'admin_vat_delete', $id, $vatNamesById);
			} else {
				$message = $this->translator->trans(
					'Opravdu si přejete trvale odstranit sazbu "%name%"? Nikde není použita.',
					['%name%' => $vat->getName()]
				);

				return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_vat_delete', $id);
			}
		} catch (\SS6\ShopBundle\Model\Pricing\Vat\Exception\VatNotFoundException $ex) {
			return new Response($this->translator->trans('Zvolené DPH neexistuje'));
		}

	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\VatFacade $vatFacade
	 * @param int $id
	 * @return array
	 */
	private function getVatNamesByIdExceptId($vatFacade, $id) {
		$vatNamesById = [];
		foreach ($vatFacade->getAllExceptId($id) as $newVat) {
			$vatNamesById[$newVat->getId()] = $newVat->getName();
		}

		return $vatNamesById;
	}

	/**
	 * @Route("/vat/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function deleteAction(Request $request, $id) {
		$newId = $request->get('newId');

		try {
			$fullName = $this->vatFacade->getById($id)->getName();

			$this->em->transactional(
				function () use ($id, $newId) {
					$this->vatFacade->deleteById($id, $newId);
				}
			);

			if ($newId === null) {
				$this->getFlashMessageSender()->addSuccessFlashTwig('DPH <strong>{{ name }}</strong> bylo smazáno', [
					'name' => $fullName,
				]);
			} else {
				$newVat = $this->vatFacade->getById($newId);
				$this->getFlashMessageSender()->addSuccessFlashTwig(
					'DPH <strong>{{ name }}</strong> bylo smazáno a bylo nahrazeno <strong>{{ newName }}</strong>.',
					[
						'name' => $fullName,
						'newName' => $newVat->getName(),
					]);
			}

		} catch (\SS6\ShopBundle\Model\Pricing\Vat\Exception\VatNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolené DPH neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_vat_list'));
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function settingsAction(Request $request) {
		$vats = $this->vatFacade->getAll();
		$form = $this->createForm(new VatSettingsFormType(
			$vats,
			PricingSetting::getRoundingTypes(),
			$this->translator
		));

		try {
			$vatSettingsFormData = [];
			$vatSettingsFormData['defaultVat'] = $this->vatFacade->getDefaultVat();
			$vatSettingsFormData['roundingType'] = $this->pricingSetting->getRoundingType();

			$form->setData($vatSettingsFormData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$vatSettingsFormData = $form->getData();
				$this->vatFacade->setDefaultVat($vatSettingsFormData['defaultVat']);
				$this->pricingSettingFacade->setRoundingType($vatSettingsFormData['roundingType']);
				$this->getFlashMessageSender()->addSuccessFlash('Nastavení DPH bylo upraveno');

				return $this->redirect($this->generateUrl('admin_vat_list'));
			}
		} catch (\SS6\ShopBundle\Model\Pricing\Exception\InvalidRoundingTypeException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Neplatné nastavení zaokrouhlování');
		}

		return $this->render('@SS6Shop/Admin/Content/Vat/vatSettings.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
