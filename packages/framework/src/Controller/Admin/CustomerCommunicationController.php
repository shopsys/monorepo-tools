<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Form\Admin\CustomerCommunication\CustomerCommunicationFormType;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Symfony\Component\HttpFoundation\Request;

class CustomerCommunicationController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    public function __construct(
        Setting $setting,
        AdminDomainTabsFacade $adminDomainTabsFacade
    ) {
        $this->setting = $setting;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
    }

    /**
     * @Route("/customer-communication/order-submitted/")
     */
    public function orderSubmittedAction(Request $request)
    {
        $domainId = $this->adminDomainTabsFacade->getSelectedDomainId();
        $orderSentPageContent = $this->setting->getForDomain(Setting::ORDER_SENT_PAGE_CONTENT, $domainId);

        $form = $this->createForm(CustomerCommunicationFormType::class, ['content' => $orderSentPageContent]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $this->setting->setForDomain(Setting::ORDER_SENT_PAGE_CONTENT, $formData['content'], $domainId);

            $this->getFlashMessageSender()->addSuccessFlash(t('Order confirmation page content modified'));

            return $this->redirectToRoute('admin_customercommunication_ordersubmitted');
        }

        return $this->render('@ShopsysFramework/Admin/Content/CustomerCommunication/orderSubmitted.html.twig', [
            'form' => $form->createView(),
            'VARIABLE_TRANSPORT_INSTRUCTIONS' => OrderFacade::VARIABLE_TRANSPORT_INSTRUCTIONS,
            'VARIABLE_PAYMENT_INSTRUCTIONS' => OrderFacade::VARIABLE_PAYMENT_INSTRUCTIONS,
            'VARIABLE_ORDER_DETAIL_URL' => OrderFacade::VARIABLE_ORDER_DETAIL_URL,
            'VARIABLE_NUMBER' => OrderFacade::VARIABLE_NUMBER,
        ]);
    }
}
