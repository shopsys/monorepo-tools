<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Pricing\Currency\CurrencySettingsFormType;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Grid\CurrencyInlineEdit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
     */
    private $confirmDeleteResponseFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Grid\CurrencyInlineEdit
     */
    private $currencyInlineEdit;

    public function __construct(
        CurrencyFacade $currencyFacade,
        CurrencyInlineEdit $currencyInlineEdit,
        ConfirmDeleteResponseFactory $confirmDeleteResponseFactory,
        Domain $domain
    ) {
        $this->currencyFacade = $currencyFacade;
        $this->currencyInlineEdit = $currencyInlineEdit;
        $this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
        $this->domain = $domain;
    }

    /**
     * @Route("/currency/list/")
     */
    public function listAction()
    {
        $grid = $this->currencyInlineEdit->getGrid();

        return $this->render('@ShopsysFramework/Admin/Content/Currency/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/currency/delete-confirm/{id}", requirements={"id" = "\d+"})
     * @param int $id
     */
    public function deleteConfirmAction($id)
    {
        try {
            $currency = $this->currencyFacade->getById($id);
            $message = t(
                'Do you really want to remove currency "%name%" permanently?',
                ['%name%' => $currency->getName()]
            );

            return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_currency_delete', $id);
        } catch (\Shopsys\FrameworkBundle\Model\Pricing\Currency\Exception\CurrencyNotFoundException $ex) {
            return new Response(t('Selected currency doesn\'t exist.'));
        }
    }

    /**
     * @Route("/currency/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $fullName = $this->currencyFacade->getById($id)->getName();
            $this->currencyFacade->deleteById($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Currency <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $fullName,
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Pricing\Currency\Exception\DeletingNotAllowedToDeleteCurrencyException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(
                t('This currency can\'t be deleted, it is set as default or is saved with order.')
            );
        } catch (\Shopsys\FrameworkBundle\Model\Pricing\Currency\Exception\CurrencyNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected currency doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_currency_list');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function settingsAction(Request $request)
    {
        $domainNames = [];

        $currencySettingsFormData = [];
        $currencySettingsFormData['defaultCurrency'] = $this->currencyFacade->getDefaultCurrency();
        $currencySettingsFormData['domainDefaultCurrencies'] = [];

        foreach ($this->domain->getAll() as $domainConfig) {
            $domainId = $domainConfig->getId();
            $currencySettingsFormData['domainDefaultCurrencies'][$domainId] =
                $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);
            $domainNames[$domainId] = $domainConfig->getName();
        }

        $form = $this->createForm(CurrencySettingsFormType::class, $currencySettingsFormData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currencySettingsFormData = $form->getData();

            $this->currencyFacade->setDefaultCurrency($currencySettingsFormData['defaultCurrency']);

            foreach ($this->domain->getAll() as $domainConfig) {
                $domainId = $domainConfig->getId();
                $this->currencyFacade->setDomainDefaultCurrency(
                    $currencySettingsFormData['domainDefaultCurrencies'][$domainId],
                    $domainId
                );
            }

            $this->getFlashMessageSender()->addSuccessFlashTwig(t('Currency settings modified'));

            return $this->redirectToRoute('admin_currency_list');
        }

        return $this->render('@ShopsysFramework/Admin/Content/Currency/currencySettings.html.twig', [
            'form' => $form->createView(),
            'domainNames' => $domainNames,
        ]);
    }
}
