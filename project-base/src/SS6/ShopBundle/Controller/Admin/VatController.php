<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Vat\DefaultVatFormType;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
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
	 * @Route("/vat/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageTwig = $this->get('ss6.shop.flash_message.twig_sender.admin');
		/* @var $flashMessageTwig \SS6\ShopBundle\Model\FlashMessage\TwigSender */

		$vatFacade = $this->get('ss6.shop.pricing.vat.vat_facade');
		/* @var $vatFacade \SS6\ShopBundle\Model\Pricing\Vat\VatFacade */

		$fullName = $vatFacade->getById($id)->getName();
		$vatFacade->deleteById($id);

		$flashMessageTwig->addSuccess('DPH <strong>{{ name }}</strong> bylo smazáno', array(
			'name' => $fullName,
		));
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

		$vats = $vatRepository->findAll();
		$form = $this->createForm(new DefaultVatFormType($vats));

		$defaultVatFormData = array();
		if (!$form->isSubmitted()) {
			$defaultVatFormData['defaultVat'] = $vatFacade->getDefaultVat();
		}
		
		$form->setData($defaultVatFormData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$defaultVatFormData = $form->getData();
			$vatFacade->setDefaultVat($defaultVatFormData['defaultVat']);

			// @codingStandardsIgnoreStart
			// TODO: redirect && $flashMessageText->addSuccess('Nastavení výchozí sazby DPH bylo upraveno');
			// @codingStandardsIgnoreStop
		}

		return $this->render('@SS6Shop/Admin/Content/Vat/defaultVat.html.twig', array(
			'form' => $form->createView(),
		));
	}

}
