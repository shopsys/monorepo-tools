<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Form\Admin\CustomerCommunication\CustomerCommunicationFormType;
use Shopsys\ShopBundle\Model\Order\OrderFacade;
use Symfony\Component\HttpFoundation\Request;

class CustomerCommunicationController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    public function __construct(
        Setting $setting,
        SelectedDomain $selectedDomain
    ) {
        $this->setting = $setting;
        $this->selectedDomain = $selectedDomain;
    }

    /**
     * @Route("/customer-communication/order-submitted/")
     */
    public function orderSubmittedAction(Request $request) {
        $data = $this->setting->getForDomain(Setting::ORDER_SUBMITTED_SETTING_NAME, $this->selectedDomain->getId());
        $form = $this->createForm(new CustomerCommunicationFormType());

        $form->setData(['content' => $data]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();
            $this->setting->setForDomain(Setting::ORDER_SUBMITTED_SETTING_NAME, $formData['content'], $this->selectedDomain->getId());

            $this->getFlashMessageSender()->addSuccessFlash(t('Order confirmation page content modified'));

            return $this->redirectToRoute('admin_customercommunication_ordersubmitted');
        }

        return $this->render('@ShopsysShop/Admin/Content/CustomerCommunication/orderSubmitted.html.twig', [
            'form' => $form->createView(),
            'VARIABLE_TRANSPORT_INSTRUCTIONS' => OrderFacade::VARIABLE_TRANSPORT_INSTRUCTIONS,
            'VARIABLE_PAYMENT_INSTRUCTIONS' => OrderFacade::VARIABLE_PAYMENT_INSTRUCTIONS,
            'VARIABLE_ORDER_DETAIL_URL' => OrderFacade::VARIABLE_ORDER_DETAIL_URL,
            'VARIABLE_NUMBER' => OrderFacade::VARIABLE_NUMBER,
        ]);
    }
}
