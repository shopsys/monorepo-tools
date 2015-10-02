<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Form\Admin\TransportAndPayment\FreeTransportAndPaymentPriceLimitsFormType;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\HttpFoundation\Request;

class TransportAndPaymentController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	public function __construct(
		Domain $domain,
		PricingSetting $pricingSetting
	) {
		$this->domain = $domain;
		$this->pricingSetting = $pricingSetting;
	}

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
		$form = $this->createForm(new FreeTransportAndPaymentPriceLimitsFormType($this->domain->getAll()));

		$formData = [];

		foreach ($this->domain->getAll() as $domainConfig) {
			$domainId = $domainConfig->getId();
			$freeTransportAndPaymentPriceLimit = $this->pricingSetting->getFreeTransportAndPaymentPriceLimit($domainId);

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

			$this->transactional(
				function () use ($subformData) {
					foreach ($this->domain->getAll() as $domainConfig) {
						$domainId = $domainConfig->getId();

						if ($subformData[$domainId][FreeTransportAndPaymentPriceLimitsFormType::FIELD_ENABLED]) {
							$priceLimit = $subformData[$domainId][FreeTransportAndPaymentPriceLimitsFormType::FIELD_PRICE_LIMIT];
						} else {
							$priceLimit = null;
						}

						$this->pricingSetting->setFreeTransportAndPaymentPriceLimit($domainId, $priceLimit);
					}
				}
			);

			$this->getFlashMessageSender()->addSuccessFlash('Nastavení dopravy a platby zdarma bylo uloženo');

			return $this->redirect($this->generateUrl('admin_transportandpayment_freetransportandpaymentlimit'));
		}

		return $this->render('@SS6Shop/Admin/Content/TransportAndPayment/freeTransportAndPaymentLimitSetting.html.twig', [
			'form' => $form->createView(),
			'domain' => $this->domain,
		]);
	}

}
