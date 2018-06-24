<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Form\Admin\TransportAndPayment\FreeTransportAndPaymentPriceLimitsFormType;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Symfony\Component\HttpFoundation\Request;

class TransportAndPaymentController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
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
    public function listAction()
    {
        return $this->render('@ShopsysFramework/Admin/Content/TransportAndPayment/list.html.twig');
    }

    /**
     * @Route("/transport-and-payment/free-transport-and-payment-limit/")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function freeTransportAndPaymentLimitAction(Request $request)
    {
        $formData = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $domainId = $domainConfig->getId();
            $freeTransportAndPaymentPriceLimit = $this->pricingSetting->getFreeTransportAndPaymentPriceLimit($domainId);

            $formData[FreeTransportAndPaymentPriceLimitsFormType::DOMAINS_SUBFORM_NAME][$domainId] = [
                FreeTransportAndPaymentPriceLimitsFormType::FIELD_ENABLED => $freeTransportAndPaymentPriceLimit !== null,
                FreeTransportAndPaymentPriceLimitsFormType::FIELD_PRICE_LIMIT => $freeTransportAndPaymentPriceLimit,
            ];
        }

        $form = $this->createForm(FreeTransportAndPaymentPriceLimitsFormType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

        return $this->render('@ShopsysFramework/Admin/Content/TransportAndPayment/freeTransportAndPaymentLimitSetting.html.twig', [
            'form' => $form->createView(),
            'domain' => $this->domain,
        ]);
    }
}
