<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\ShopBundle\Form\Admin\Script\GoogleAnalyticsScriptFormType;
use Shopsys\ShopBundle\Form\Admin\Script\ScriptFormType;
use Shopsys\ShopBundle\Model\Script\Script;
use Shopsys\ShopBundle\Model\Script\ScriptData;
use Shopsys\ShopBundle\Model\Script\ScriptFacade;
use Symfony\Component\HttpFoundation\Request;

class ScriptController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Script\ScriptFacade
     */
    private $scriptFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    public function __construct(
        ScriptFacade $scriptFacade,
        GridFactory $gridFactory,
        SelectedDomain $selectedDomain
    ) {
        $this->scriptFacade = $scriptFacade;
        $this->gridFactory = $gridFactory;
        $this->selectedDomain = $selectedDomain;
    }

    /**
     * @Route("/script/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request) {
        $form = $this->createForm(new ScriptFormType());
        $scriptData = new ScriptData();
        $scriptVariables = $this->getOrderSentPageScriptVariableLabelsIndexedByVariables();

        $form->setData($scriptData);
        $form->handleRequest($request);

        if ($form->isValid()) {
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

        return $this->render('@ShopsysShop/Admin/Content/Script/new.html.twig', [
            'form' => $form->createView(),
            'scriptVariables' => $scriptVariables,
        ]);
    }

    /**
     * @Route("/script/edit/{scriptId}")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $scriptId
     */
    public function editAction(Request $request, $scriptId) {
        $script = $this->scriptFacade->getById($scriptId);
        $scriptVariables = $this->getOrderSentPageScriptVariableLabelsIndexedByVariables();

        $form = $this->createForm(new ScriptFormType());
        $scriptData = new ScriptData();
        $scriptData->setFromEntity($script);

        $form->setData($scriptData);
        $form->handleRequest($request);

        if ($form->isValid()) {
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

        return $this->render('@ShopsysShop/Admin/Content/Script/edit.html.twig', [
            'script' => $script,
            'form' => $form->createView(),
            'scriptVariables' => $scriptVariables,
        ]);
    }

    /**
     * @Route("/script/list/")
     */
    public function listAction() {
        $dataSource = new QueryBuilderDataSource($this->scriptFacade->getAllQueryBuilder(), 's.id');

        $grid = $this->gridFactory->create('scriptsList', $dataSource);

        $grid->addColumn('name', 's.name', t('Script name'));
        $grid->addColumn('placement', 's.placement', t('Location'));
        $grid->addEditActionColumn('admin_script_edit', ['scriptId' => 's.id']);
        $grid->addDeleteActionColumn('admin_script_delete', ['scriptId' => 's.id'])
            ->setConfirmMessage(t('Do you really want to remove this script?'));

        $grid->setTheme('@ShopsysShop/Admin/Content/Script/listGrid.html.twig', [
            'PLACEMENT_ORDER_SENT_PAGE' => Script::PLACEMENT_ORDER_SENT_PAGE,
            'PLACEMENT_ALL_PAGES' => Script::PLACEMENT_ALL_PAGES,
        ]);

        return $this->render('@ShopsysShop/Admin/Content/Script/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/script/delete/{scriptId}")
     * @param int $scriptId
     */
    public function deleteAction($scriptId) {
        try {
            $script = $this->scriptFacade->getById($scriptId);

            $this->scriptFacade->delete($scriptId);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Script <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $script->getName(),
                ]
            );
        } catch (\Shopsys\ShopBundle\Model\Script\Exception\ScriptNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected script doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_script_list');
    }

    /**
     * @Route("/script/google-analytics/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function googleAnalyticsAction(Request $request) {
        $domainId = $this->selectedDomain->getId();
        $form = $this->createForm(new GoogleAnalyticsScriptFormType());
        $formData = ['trackingId' => $this->scriptFacade->getGoogleAnalyticsTrackingId($domainId)];

        $form->setData($formData);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->scriptFacade->setGoogleAnalyticsTrackingId($form->getData()['trackingId'], $domainId);
            $this->getFlashMessageSender()->addSuccessFlashTwig(t('Google script code set'));
        }

        return $this->render('@ShopsysShop/Admin/Content/Script/googleAnalytics.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return string[]
     */
    private function getOrderSentPageScriptVariableLabelsIndexedByVariables() {
        return [
            ScriptFacade::VARIABLE_NUMBER => t('Order number'),
            ScriptFacade::VARIABLE_TOTAL_PRICE => t('Total order price including VAT'),
        ];
    }
}
