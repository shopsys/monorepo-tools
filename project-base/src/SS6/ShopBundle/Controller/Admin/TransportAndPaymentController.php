<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\TransportAndPayment\FreeTransportAndPaymentPriceLimitsFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TransportAndPaymentController extends Controller {

	/**
	 * @Route("/transport_and_payment/list/")
	 */
	public function listAction() {
		return $this->render('@SS6Shop/Admin/Content/TransportAndPayment/list.html.twig');
	}

	/**
	 * @Route("/transport_and_payment/free_transport_and_payment_limit/")
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function freeTransportAndPaymentLimitAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$pricingSetting = $this->get('ss6.shop.pricing.pricing_setting');
		/* @var $pricingSetting \SS6\ShopBundle\Model\Pricing\PricingSetting */

		$form = $this->createForm(new FreeTransportAndPaymentPriceLimitsFormType($domain->getAll()));

		$formData = [];

		foreach ($domain->getAll() as $domainConfig) {
			$domainId = $domainConfig->getId();
			$freeTransportAndPaymentPriceLimit = $pricingSetting->getFreeTransportAndPaymentPriceLimit($domainId);

			$formData[FreeTransportAndPaymentPriceLimitsFormType::DOMAINS_SUBFORM_NAME][$domainId] = [
				FreeTransportAndPaymentPriceLimitsFormType::FIELD_ENABLED => $freeTransportAndPaymentPriceLimit !== null,
				FreeTransportAndPaymentPriceLimitsFormType::FIELD_PRICE_LIMIT => $freeTransportAndPaymentPriceLimit,
			];
		}

		$form->setData($formData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$formData = $form->getData();
			$subformData = $formData[FreeTransportAndPaymentPriceLimitsFormType::DOMAINS_SUBFORM_NAME];

			foreach ($domain->getAll() as $domainConfig) {
				$domainId = $domainConfig->getId();

				if ($subformData[$domainId][FreeTransportAndPaymentPriceLimitsFormType::FIELD_ENABLED]) {
					$priceLimit = $subformData[$domainId][FreeTransportAndPaymentPriceLimitsFormType::FIELD_PRICE_LIMIT];
				} else {
					$priceLimit = null;
				}

				$pricingSetting->setFreeTransportAndPaymentPriceLimit($domainId, $priceLimit);
			}

			$flashMessageSender->addSuccessFlash('Nastavení dopravy a platby zdarma bylo uloženo');
			return $this->redirect($this->generateUrl('admin_transportandpayment_freetransportandpaymentlimit'));
		}

		return $this->render('@SS6Shop/Admin/Content/TransportAndPayment/freeTransportAndPaymentLimitSetting.html.twig', [
			'form' => $form->createView(),
			'domain' => $domain,
		]);
	}

}
