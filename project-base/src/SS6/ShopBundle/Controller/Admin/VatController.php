<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Vat\DefaultVatFormType;
use SS6\ShopBundle\Form\Admin\Vat\RoundingSettingFormType;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class VatController extends Controller {

	/**
	 * @Route("/vat/list/")
	 */
	public function listAction() {
		$vatInlineEdit = $this->get('ss6.shop.pricing.vat.vat_inline_edit');
		/* @var $vatInlineEdit \SS6\ShopBundle\Model\Pricing\Vat\VatInlineEdit */

		$grid = $vatInlineEdit->getGrid();
		
		return $this->render('@SS6Shop/Admin/Content/Vat/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
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

		$vat = $vatFacade->getById($id);
		if ($vatFacade->isVatUsed($vat)) {
			$message = 'Pro odstranění sazby "' . $vat->getName() . '" musíte zvolit, která se má všude, '
				. 'kde je aktuálně používaná nastavit. Jakou sazbu místo ní chcete nastavit?';
			$vatNamesById = array();
			foreach ($vatFacade->getAllExceptId($id) as $newVat) {
				$vatNamesById[$newVat->getId()] = $newVat->getName();
			}
			return $confirmDeleteResponseFactory->createSetNewAndDeleteResponse($message, 'admin_vat_delete', $id, $vatNamesById);
		} else {
			$message = 'Opravdu si přejete trvale odstranit sazbu "' . $vat->getName() . '"? Nikde není použita.';
			return $confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_vat_delete', $id);
		}
	}

	/**
	 * @Route("/vat/delete/{id}/{newId}", requirements={"id" = "\d+", "newId" = "\d+"})
	 * @param int $id
	 * @param int|null $newId
	 */
	public function deleteAction($id, $newId = null) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$vatFacade = $this->get('ss6.shop.pricing.vat.vat_facade');
		/* @var $vatFacade \SS6\ShopBundle\Model\Pricing\Vat\VatFacade */

		$fullName = $vatFacade->getById($id)->getName();
		
		$vatFacade->deleteById($id, $newId);

		if ($newId === null) {
			$flashMessageSender->addSuccessTwig('DPH <strong>{{ name }}</strong> bylo smazáno', array(
				'name' => $fullName,
			));
		} else {
			$newVat = $vatFacade->getById($newId);
			$flashMessageSender->addSuccessTwig(
				'DPH <strong>{{ name }}</strong> bylo smazáno a bylo nahrazeno <strong>{{ newName }}</strong>.',
				array(
					'name' => $fullName,
					'newName' => $newVat->getName(),
				));
		}
		return $this->redirect($this->generateUrl('admin_vat_list'));
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function defaultVatAction(Request $request) {
		$vatRepository = $this->get('ss6.shop.pricing.vat.vat_repository');
		/* @var $vatRepository \SS6\ShopBundle\Model\Pricing\Vat\VatRepository */
		$vatFacade = $this->get('ss6.shop.pricing.vat.vat_facade');
		/* @var $vatFacade \SS6\ShopBundle\Model\Pricing\Vat\VatFacade */
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$vats = $vatRepository->findAll();
		$form = $this->createForm(new DefaultVatFormType($vats));

		$defaultVatFormData = array();
		$defaultVatFormData['defaultVat'] = $vatFacade->getDefaultVat();
		
		$form->setData($defaultVatFormData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$defaultVatFormData = $form->getData();
			$vatFacade->setDefaultVat($defaultVatFormData['defaultVat']);
			$flashMessageSender->addSuccess('Nastavení výchozí sazby DPH bylo upraveno');
			
			return $this->redirect($this->generateUrl('admin_vat_list'));
		}

		return $this->render('@SS6Shop/Admin/Content/Vat/defaultVat.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function roundingSettingAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$pricingSetting = $this->get('ss6.shop.pricing.pricing_setting');
		/* @var $pricingSetting \SS6\ShopBundle\Model\Pricing\PricingSetting */
		$pricingSettingFacade = $this->get('ss6.shop.pricing.pricing_setting_facade');
		/* @var $pricingSettingFacade \SS6\ShopBundle\Model\Pricing\PricingSettingFacade */

		$form = $this->createForm(new RoundingSettingFormType(PricingSetting::getRoundingTypes()));

		try {
			$roundingSettingFormData = array();
			$roundingSettingFormData['roundingType'] = $pricingSetting->getRoundingType();

			$form->setData($roundingSettingFormData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$roundingSettingFormData = $form->getData();
				$pricingSettingFacade->setRoundingType($roundingSettingFormData['roundingType']);

				$flashMessageSender->addSuccess('Nastavení zaokrouhlování bylo upraveno');
				return $this->redirect($this->generateUrl('admin_vat_list'));
			}
		} catch (\SS6\ShopBundle\Model\Pricing\Exception\InvalidRoundingTypeException $ex) {
			$flashMessageSender->addError('Neplatné nastavení zaokrouhlování');
		}

		return $this->render('@SS6Shop/Admin/Content/Vat/roundingSetting.html.twig', array(
			'form' => $form->createView(),
		));
	}

}
