<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Form\Admin\Cookies\CookiesSettingFormTypeFactory;
use Shopsys\ShopBundle\Model\Cookies\CookiesFacade;
use Symfony\Component\HttpFoundation\Request;

class CookiesController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    /**
     * @var \Shopsys\ShopBundle\Form\Admin\Cookies\CookiesSettingFormTypeFactory
     */
    private $cookiesSettingFormTypeFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Cookies\CookiesFacade
     */
    private $cookiesFacade;

    public function __construct(
        SelectedDomain $selectedDomain,
        CookiesSettingFormTypeFactory $cookiesSettingFormTypeFactory,
        CookiesFacade $cookiesFacade
    ) {
        $this->selectedDomain = $selectedDomain;
        $this->cookiesSettingFormTypeFactory = $cookiesSettingFormTypeFactory;
        $this->cookiesFacade = $cookiesFacade;
    }

    /**
     * @Route("/cookies/setting/")
     */
    public function settingAction(Request $request) {
        $selectedDomainId = $this->selectedDomain->getId();
        $cookiesArticle = $this->cookiesFacade->findCookiesArticleByDomainId($selectedDomainId);

        $cookiesSettingData = [
            'cookiesArticle' => $cookiesArticle,
        ];

        $form = $this->createForm($this->cookiesSettingFormTypeFactory->createForDomain($selectedDomainId));
        $form->setData($cookiesSettingData);
        $form->handleRequest($request);

        if ($form->isValid()) {
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

        return $this->render('@ShopsysShop/Admin/Content/Cookies/setting.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
