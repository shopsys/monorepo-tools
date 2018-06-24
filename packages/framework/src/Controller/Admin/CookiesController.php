<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Form\Admin\Cookies\CookiesSettingFormType;
use Shopsys\FrameworkBundle\Model\Cookies\CookiesFacade;
use Symfony\Component\HttpFoundation\Request;

class CookiesController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cookies\CookiesFacade
     */
    private $cookiesFacade;

    public function __construct(
        AdminDomainTabsFacade $adminDomainTabsFacade,
        CookiesFacade $cookiesFacade
    ) {
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->cookiesFacade = $cookiesFacade;
    }

    /**
     * @Route("/cookies/setting/")
     */
    public function settingAction(Request $request)
    {
        $selectedDomainId = $this->adminDomainTabsFacade->getSelectedDomainId();
        $cookiesArticle = $this->cookiesFacade->findCookiesArticleByDomainId($selectedDomainId);

        $form = $this->createForm(CookiesSettingFormType::class, ['cookiesArticle' => $cookiesArticle], [
            'domain_id' => $selectedDomainId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cookiesArticle = $form->getData()['cookiesArticle'];

            $this->cookiesFacade->setCookiesArticleOnDomain(
                $cookiesArticle,
                $selectedDomainId
            );

            $this->getFlashMessageSender()->addSuccessFlashTwig(t('Cookies information settings modified.'));
            return $this->redirectToRoute('admin_cookies_setting');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Cookies/setting.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
