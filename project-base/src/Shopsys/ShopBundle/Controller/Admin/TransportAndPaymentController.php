<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\Admin\TransportAndPayment\FreeTransportAndPaymentPriceLimitsFormType;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\HttpFoundation\Request;

class TransportAndPaymentController extends AdminBaseController
{

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\PricingSetting
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
     * @Route("/transport-and-payment/list/")
     */
    public function listAction() {
        return $this->render('@ShopsysShop/Admin/Content/TransportAndPayment/list.html.twig');
    }

    /**
     * @Route("/transport-and-payment/free-transport-and-payment-limit/")
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

            foreach ($this->domain->getAll() as $domainConfig) {
                $domainId = $domainConfig->getId();

                if ($subformData[$domainId][FreeTransportAndPaymentPriceLimitsFormType::FIELD_ENABLED]) {
                    $priceLimit = $subformData[$domainId][FreeTransportAndPaymentPriceLimitsFormType::FIELD_PRICE_LIMIT];
                } else {
                    $priceLimit = null;
                }

                $this->pricingSetting->setFreeTransportAndPaymentPriceLimit($domainId, $priceLimit);
            }

            $this->getFlashMessageSender()->addSuccessFlash(t('Free shipping and payment settings saved'));

            return $this->redirectToRoute('admin_transportandpayment_freetransportandpaymentlimit');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlash(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysShop/Admin/Content/TransportAndPayment/freeTransportAndPaymentLimitSetting.html.twig', [
            'form' => $form->createView(),
            'domain' => $this->domain,
        ]);
    }
}
