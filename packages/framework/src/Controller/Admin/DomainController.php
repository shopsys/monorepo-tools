<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Domain\DomainFacade;
use Shopsys\FrameworkBundle\Component\FlashMessage\ErrorService;
use Shopsys\FrameworkBundle\Component\Grid\ArrayDataSource;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Form\Admin\Domain\DomainFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DomainController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\DomainFacade
     */
    private $domainFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FlashMessage\ErrorService
     */
    private $errorService;

    public function __construct(
        Domain $domain,
        AdminDomainTabsFacade $adminDomainTabsFacade,
        GridFactory $gridFactory,
        DomainFacade $domainFacade,
        ErrorService $errorService
    ) {
        $this->domain = $domain;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->gridFactory = $gridFactory;
        $this->domainFacade = $domainFacade;
        $this->errorService = $errorService;
    }

    public function domainTabsAction()
    {
        return $this->render('@ShopsysFramework/Admin/Inline/Domain/tabs.html.twig', [
            'domainConfigs' => $this->domain->getAll(),
            'selectedDomainId' => $this->adminDomainTabsFacade->getSelectedDomainId(),
        ]);
    }

    /**
     * @Route("/multidomain/select-domain/{id}", requirements={"id" = "\d+"})
     * @param Request $request
     */
    public function selectDomainAction(Request $request, $id)
    {
        $id = (int)$id;

        $this->adminDomainTabsFacade->setSelectedDomainId($id);

        $referer = $request->server->get('HTTP_REFERER');
        if ($referer === null) {
            return $this->redirectToRoute('admin_dashboard');
        } else {
            return $this->redirect($referer);
        }
    }

    /**
     * @Route("/domain/list")
     */
    public function listAction()
    {
        $dataSource = new ArrayDataSource($this->loadData(), 'id');

        $grid = $this->gridFactory->create('domainsList', $dataSource);

        $grid->addColumn('name', 'name', t('Domain name'));
        $grid->addColumn('locale', 'locale', t('Language'));
        $grid->addColumn('icon', 'icon', t('Icon'));

        $grid->setTheme('@ShopsysFramework/Admin/Content/Domain/listGrid.html.twig');

        return $this->render('@ShopsysFramework/Admin/Content/Domain/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/domain/edit/{id}", requirements={"id" = "\d+"}, condition="request.isXmlHttpRequest()")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function editAction(Request $request, $id)
    {
        $id = (int)$id;
        $domain = $this->domain->getDomainConfigById($id);

        $form = $this->createForm(DomainFormType::class, null, [
            'action' => $this->generateUrl('admin_domain_edit', ['id' => $id]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $files = $form->getData()[DomainFormType::FIELD_ICON]->uploadedFiles;
                if (count($files) !== 0) {
                    $iconName = reset($files);

                    $this->domainFacade->editIcon($id, $iconName);
                }

                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Domain <strong>{{ name }}</strong> modified'),
                    ['name' => $domain->getName()]
                );

                return new JsonResponse(['result' => 'valid']);
            } catch (\Shopsys\FrameworkBundle\Component\Image\Processing\Exception\FileIsNotSupportedImageException $ex) {
                $this->getFlashMessageSender()->addErrorFlash(t('File type not supported.'));
            } catch (\Shopsys\FrameworkBundle\Component\FileUpload\Exception\MoveToFolderFailedException $ex) {
                $this->getFlashMessageSender()->addErrorFlash(t('File upload failed, try again please.'));
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $flashMessageBag = $this->get('shopsys.shop.component.flash_message.bag.admin');
            return new JsonResponse([
                'result' => 'invalid',
                'errors' => $this->errorService->getAllErrorsAsArray($form, $flashMessageBag),
            ]);
        }

        return $this->render('@ShopsysFramework/Admin/Content/Domain/edit.html.twig', [
            'form' => $form->createView(),
            'domain' => $domain,
        ]);
    }

    private function loadData()
    {
        $data = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $data[] = [
                'id' => $domainConfig->getId(),
                'name' => $domainConfig->getName(),
                'locale' => $domainConfig->getLocale(),
                'icon' => null,
            ];
        }

        return $data;
    }
}
