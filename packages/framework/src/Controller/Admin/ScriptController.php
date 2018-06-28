<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\Form\Admin\Script\GoogleAnalyticsScriptFormType;
use Shopsys\FrameworkBundle\Form\Admin\Script\ScriptFormType;
use Shopsys\FrameworkBundle\Model\Script\Script;
use Shopsys\FrameworkBundle\Model\Script\ScriptData;
use Shopsys\FrameworkBundle\Model\Script\ScriptFacade;
use Symfony\Component\HttpFoundation\Request;

class ScriptController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Script\ScriptFacade
     */
    private $scriptFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    public function __construct(
        ScriptFacade $scriptFacade,
        GridFactory $gridFactory,
        AdminDomainTabsFacade $adminDomainTabsFacade
    ) {
        $this->scriptFacade = $scriptFacade;
        $this->gridFactory = $gridFactory;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
    }

    /**
     * @Route("/script/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(ScriptFormType::class, new ScriptData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scriptData = $form->getData();

            $script = $this->scriptFacade->create($scriptData);

            $this->getFlashMessageSender()
                ->addSuccessFlashTwig(
                    t('Script <a href="{{ url }}"><strong>{{ name }}</strong></a> created'),
                    [
                        'name' => $script->getName(),
                        'url' => $this->generateUrl('admin_script_edit', ['scriptId' => $script->getId()]),
                    ]
                );

            return $this->redirectToRoute('admin_script_list');
        }

        return $this->render('@ShopsysFramework/Admin/Content/Script/new.html.twig', [
            'form' => $form->createView(),
            'scriptVariables' => $this->getOrderSentPageScriptVariableLabelsIndexedByVariables(),
        ]);
    }

    /**
     * @Route("/script/edit/{scriptId}")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $scriptId
     */
    public function editAction(Request $request, $scriptId)
    {
        $script = $this->scriptFacade->getById($scriptId);
        $scriptData = new ScriptData();
        $scriptData->setFromEntity($script);

        $form = $this->createForm(ScriptFormType::class, $scriptData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scriptData = $form->getData();

            $script = $this->scriptFacade->edit($scriptId, $scriptData);

            $this->getFlashMessageSender()
                ->addSuccessFlashTwig(
                    t('Script <a href="{{ url }}"><strong>{{ name }}</strong></a> modified'),
                    [
                        'name' => $script->getName(),
                        'url' => $this->generateUrl('admin_script_edit', ['scriptId' => $scriptId]),
                    ]
                );
            return $this->redirectToRoute('admin_script_list');
        }

        return $this->render('@ShopsysFramework/Admin/Content/Script/edit.html.twig', [
            'script' => $script,
            'form' => $form->createView(),
            'scriptVariables' => $this->getOrderSentPageScriptVariableLabelsIndexedByVariables(),
        ]);
    }

    /**
     * @Route("/script/list/")
     */
    public function listAction()
    {
        $dataSource = new QueryBuilderDataSource($this->scriptFacade->getAllQueryBuilder(), 's.id');

        $grid = $this->gridFactory->create('scriptsList', $dataSource);

        $grid->addColumn('name', 's.name', t('Script name'));
        $grid->addColumn('placement', 's.placement', t('Location'));
        $grid->addEditActionColumn('admin_script_edit', ['scriptId' => 's.id']);
        $grid->addDeleteActionColumn('admin_script_delete', ['scriptId' => 's.id'])
            ->setConfirmMessage(t('Do you really want to remove this script?'));

        $grid->setTheme('@ShopsysFramework/Admin/Content/Script/listGrid.html.twig', [
            'PLACEMENT_ORDER_SENT_PAGE' => Script::PLACEMENT_ORDER_SENT_PAGE,
            'PLACEMENT_ALL_PAGES' => Script::PLACEMENT_ALL_PAGES,
        ]);

        return $this->render('@ShopsysFramework/Admin/Content/Script/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/script/delete/{scriptId}")
     * @param int $scriptId
     */
    public function deleteAction($scriptId)
    {
        try {
            $script = $this->scriptFacade->getById($scriptId);

            $this->scriptFacade->delete($scriptId);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Script <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $script->getName(),
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Script\Exception\ScriptNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected script doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_script_list');
    }

    /**
     * @Route("/script/google-analytics/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function googleAnalyticsAction(Request $request)
    {
        $domainId = $this->adminDomainTabsFacade->getSelectedDomainId();
        $formData = ['trackingId' => $this->scriptFacade->getGoogleAnalyticsTrackingId($domainId)];

        $form = $this->createForm(GoogleAnalyticsScriptFormType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->scriptFacade->setGoogleAnalyticsTrackingId($form->getData()['trackingId'], $domainId);
            $this->getFlashMessageSender()->addSuccessFlashTwig(t('Google script code set'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Script/googleAnalytics.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return string[]
     */
    private function getOrderSentPageScriptVariableLabelsIndexedByVariables()
    {
        return [
            ScriptFacade::VARIABLE_NUMBER => t('Order number'),
            ScriptFacade::VARIABLE_TOTAL_PRICE => t('Total order price including VAT'),
        ];
    }
}
