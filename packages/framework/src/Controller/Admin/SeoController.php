<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Form\Admin\Seo\SeoSettingFormType;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\HttpFoundation\Request;

class SeoController extends AdminBaseController
{

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    public function __construct(
        SeoSettingFacade $seoSettingFacade,
        AdminDomainTabsFacade $adminDomainTabsFacade
    ) {
        $this->seoSettingFacade = $seoSettingFacade;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
    }

    /**
     * @Route("/seo/")
     */
    public function indexAction(Request $request)
    {
        $domainId = $this->adminDomainTabsFacade->getSelectedDomainId();
        $seoSettingData = [
            'title' => $this->seoSettingFacade->getTitleMainPage($domainId),
            'metaDescription' => $this->seoSettingFacade->getDescriptionMainPage($domainId),
            'titleAddOn' => $this->seoSettingFacade->getTitleAddOn($domainId),
        ];

        $form = $this->createForm(SeoSettingFormType::class, $seoSettingData, ['domain_id' => $domainId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seoSettingData = $form->getData();

            $this->seoSettingFacade->setTitleMainPage($seoSettingData['title'], $domainId);
            $this->seoSettingFacade->setDescriptionMainPage($seoSettingData['metaDescription'], $domainId);
            $this->seoSettingFacade->setTitleAddOn($seoSettingData['titleAddOn'], $domainId);

            $this->getFlashMessageSender()->addSuccessFlash(t('SEO attributes settings modified'));

            return $this->redirectToRoute('admin_seo_index');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Seo/seoSetting.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
