<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Vat\VatSettingsFormType;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VatController extends Controller {

	/**
	 * @Route("/vat/list/")
	 */
	public function listAction() {
		$vatInlineEdit = $this->get('ss6.shop.pricing.vat.vat_inline_edit');
		/* @var $vatInlineEdit \SS6\ShopBundle\Model\Pricing\Vat\VatInlineEdit */

		$grid = $vatInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Vat/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/vat/delete_confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		$vatFacade = $this->get('ss6.shop.pricing.vat.vat_facade');
		/* @var $vatFacade \SS6\ShopBundle\Model\Pricing\Vat\VatFacade */
		$confirmDeleteResponseFactory = $this->get('ss6.shop.confirm_delete.confirm_delete_response_factory');
		/* @var $confirmDeleteResponseFactory \SS6\ShopBundle\Model\ConfirmDelete\ConfirmDeleteResponseFactory */;

		try {
			$vat = $vatFacade->getById($id);
			if ($vatFacade->isVatUsed($vat)) {
				$message = 'Pro odstranění sazby "' . $vat->getName() . '" musíte zvolit, která se má všude, '
					. 'kde je aktuálně používaná nastavit. Po změně sazby DPH dojde k přepočtu cen zboží '
					. '- základní cena s DPH zůstane zachována. Jakou sazbu místo ní chcete nastavit?';
				$vatNamesById = $this->getVatNamesByIdExceptId($vatFacade, $id);
				return $confirmDeleteResponseFactory->createSetNewAndDeleteResponse($message, 'admin_vat_delete', $id, $vatNamesById);
			} else {
				$message = 'Opravdu si přejete trvale odstranit sazbu "' . $vat->getName() . '"? Nikde není použita.';
				return $confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_vat_delete', $id);
			}
		} catch (\SS6\ShopBundle\Model\Pricing\Vat\Exception\VatNotFoundException $ex) {
			return new Response('Zvolené DPH neexistuje');
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
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function deleteAction(Request $request, $id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$vatFacade = $this->get('ss6.shop.pricing.vat.vat_facade');
		/* @var $vatFacade \SS6\ShopBundle\Model\Pricing\Vat\VatFacade */

		$newId = $request->get('newId');

		try {
			$fullName = $vatFacade->getById($id)->getName();

			$vatFacade->deleteById($id, $newId);

			if ($newId === null) {
				$flashMessageSender->addSuccessFlashTwig('DPH <strong>{{ name }}</strong> bylo smazáno', [
					'name' => $fullName,
				]);
			} else {
				$newVat = $vatFacade->getById($newId);
				$flashMessageSender->addSuccessFlashTwig(
					'DPH <strong>{{ name }}</strong> bylo smazáno a bylo nahrazeno <strong>{{ newName }}</strong>.',
					[
						'name' => $fullName,
						'newName' => $newVat->getName(),
					]);
			}

		} catch (\SS6\ShopBundle\Model\Pricing\Vat\Exception\VatNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolené DPH neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_vat_list'));
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function settingsAction(Request $request) {
		$vatFacade = $this->get('ss6.shop.pricing.vat.vat_facade');
		/* @var $vatFacade \SS6\ShopBundle\Model\Pricing\Vat\VatFacade */
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$pricingSetting = $this->get('ss6.shop.pricing.pricing_setting');
		/* @var $pricingSetting \SS6\ShopBundle\Model\Pricing\PricingSetting */
		$pricingSettingFacade = $this->get('ss6.shop.pricing.pricing_setting_facade');
		/* @var $pricingSettingFacade \SS6\ShopBundle\Model\Pricing\PricingSettingFacade */
		$translator = $this->get('translator');
		/* @var $translator \Symfony\Component\Translation\TranslatorInterface */

		$vats = $vatFacade->getAll();
		$form = $this->createForm(new VatSettingsFormType(
			$vats,
			PricingSetting::getRoundingTypes(),
			$translator
		));

		try {
			$vatSettingsFormData = [];
			$vatSettingsFormData['defaultVat'] = $vatFacade->getDefaultVat();
			$vatSettingsFormData['roundingType'] = $pricingSetting->getRoundingType();

			$form->setData($vatSettingsFormData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$vatSettingsFormData = $form->getData();
				$vatFacade->setDefaultVat($vatSettingsFormData['defaultVat']);
				$pricingSettingFacade->setRoundingType($vatSettingsFormData['roundingType']);
				$flashMessageSender->addSuccessFlash('Nastavení DPH bylo upraveno');

				return $this->redirect($this->generateUrl('admin_vat_list'));
			}
		} catch (\SS6\ShopBundle\Model\Pricing\Exception\InvalidRoundingTypeException $ex) {
			$flashMessageSender->addErrorFlash('Neplatné nastavení zaokrouhlování');
		}

		return $this->render('@SS6Shop/Admin/Content/Vat/vatSettings.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
