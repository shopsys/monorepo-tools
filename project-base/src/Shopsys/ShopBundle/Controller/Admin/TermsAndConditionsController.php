<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\ShopBundle\Form\Admin\TermsAndConditions\TermsAndConditionsSettingFormType;
use Shopsys\ShopBundle\Model\TermsAndConditions\TermsAndConditionsFacade;
use Symfony\Component\HttpFoundation\Request;

class TermsAndConditionsController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\TermsAndConditions\TermsAndConditionsFacade
     */
    private $termsAndConditionsFacade;

    public function __construct(
        AdminDomainTabsFacade $adminDomainTabsFacade,
        TermsAndConditionsFacade $termsAndConditionsFacade
    ) {
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->termsAndConditionsFacade = $termsAndConditionsFacade;
    }

    /**
     * @Route("/terms-and-conditions/setting/")
     */
    public function settingAction(Request $request)
    {
        $domainId = $this->adminDomainTabsFacade->getSelectedDomainId();
        $termsAndConditionsArticle = $this->termsAndConditionsFacade->findTermsAndConditionsArticleByDomainId($domainId);

        $termsAndConditionsSettingData = [
            'termsAndConditionsArticle' => $termsAndConditionsArticle,
        ];

        $form = $this->createForm(TermsAndConditionsSettingFormType::class, $termsAndConditionsSettingData, [
            'domain_id' => $domainId,
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $termsAndConditionsArticle = $form->getData()['termsAndConditionsArticle'];

            $this->termsAndConditionsFacade->setTermsAndConditionsArticleOnDomain(
                $termsAndConditionsArticle,
                $domainId
            );

            $this->getFlashMessageSender()->addSuccessFlashTwig(t('Terms and conditions settings modified.'));
            return $this->redirectToRoute('admin_termsandconditions_setting');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysShop/Admin/Content/TermsAndConditions/setting.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
