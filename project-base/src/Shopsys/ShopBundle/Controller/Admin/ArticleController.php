<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\ShopBundle\Form\Admin\Article\ArticleFormType;
use Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\ShopBundle\Model\AdminNavigation\MenuItem;
use Shopsys\ShopBundle\Model\Article\ArticleDataFactory;
use Shopsys\ShopBundle\Model\Article\ArticleFacade;
use Shopsys\ShopBundle\Model\Article\ArticlePlacementList;
use Shopsys\ShopBundle\Model\Cookies\CookiesFacade;
use Shopsys\ShopBundle\Model\TermsAndConditions\TermsAndConditionsFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticleFacade
     */
    private $articleFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticleDataFactory
     */
    private $articleDataFactory;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    /**
     * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
     */
    private $confirmDeleteResponseFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\TermsAndConditions\TermsAndConditionsFacade
     */
    private $termsAndConditionsFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Cookies\CookiesFacade
     */
    private $cookiesFacade;

    public function __construct(
        ArticleFacade $articleFacade,
        ArticleDataFactory $articleDataFactory,
        GridFactory $gridFactory,
        SelectedDomain $selectedDomain,
        Breadcrumb $breadcrumb,
        ConfirmDeleteResponseFactory $confirmDeleteResponseFactory,
        TermsAndConditionsFacade $termsAndConditionsFacade,
        CookiesFacade $cookiesFacade
    ) {
        $this->articleFacade = $articleFacade;
        $this->articleDataFactory = $articleDataFactory;
        $this->gridFactory = $gridFactory;
        $this->selectedDomain = $selectedDomain;
        $this->breadcrumb = $breadcrumb;
        $this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
        $this->termsAndConditionsFacade = $termsAndConditionsFacade;
        $this->cookiesFacade = $cookiesFacade;
    }

    /**
     * @Route("/article/edit/{id}", requirements={"id" = "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     */
    public function editAction(Request $request, $id)
    {
        $article = $this->articleFacade->getById($id);
        $articleData = $this->articleDataFactory->createFromArticle($article);

        $form = $this->createForm(ArticleFormType::class, $articleData, [
            'article' => $article,
            'domain_id' => $this->selectedDomain->getId(),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->articleFacade->edit($id, $articleData);

            $this->getFlashMessageSender()
                ->addSuccessFlashTwig(
                    t('Article <strong><a href="{{ url }}">{{ name }}</a></strong> modified'),
                    [
                        'name' => $article->getName(),
                        'url' => $this->generateUrl('admin_article_edit', ['id' => $article->getId()]),
                    ]
                );
            return $this->redirectToRoute('admin_article_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumb->overrideLastItem(new MenuItem(t('Editing article - %name%', ['%name%' => $article->getName()])));

        return $this->render('@ShopsysShop/Admin/Content/Article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    /**
     * @Route("/article/list/")
     */
    public function listAction()
    {
        $gridTop = $this->getGrid(ArticlePlacementList::PLACEMENT_TOP_MENU);
        $gridFooter = $this->getGrid(ArticlePlacementList::PLACEMENT_FOOTER);
        $gridNone = $this->getGrid(ArticlePlacementList::PLACEMENT_NONE);
        $articlesCountOnSelectedDomain = $this->articleFacade->getAllArticlesCountByDomainId($this->selectedDomain->getId());

        return $this->render('@ShopsysShop/Admin/Content/Article/list.html.twig', [
            'gridViewTop' => $gridTop->createView(),
            'gridViewFooter' => $gridFooter->createView(),
            'gridViewNone' => $gridNone->createView(),
            'articlesCountOnSelectedDomain' => $articlesCountOnSelectedDomain,
        ]);
    }

    /**
     * @Route("/article/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $articleData = $this->articleDataFactory->createDefault();

        $form = $this->createForm(ArticleFormType::class, $articleData, [
            'article' => null,
            'domain_id' => $this->selectedDomain->getId(),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $articleData = $form->getData();

            $article = $this->articleFacade->create($articleData);

            $this->getFlashMessageSender()
                ->addSuccessFlashTwig(
                    t('Article <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
                    [
                        'name' => $article->getName(),
                        'url' => $this->generateUrl('admin_article_edit', ['id' => $article->getId()]),
                    ]
                );
            return $this->redirectToRoute('admin_article_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysShop/Admin/Content/Article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $fullName = $this->articleFacade->getById($id)->getName();

            $this->articleFacade->delete($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Article <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $fullName,
                ]
            );
        } catch (\Shopsys\ShopBundle\Model\Article\Exception\ArticleNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected article doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_article_list');
    }

    /**
     * @Route("/article/delete-confirm/{id}", requirements={"id" = "\d+"})
     * @param int $id
     */
    public function deleteConfirmAction($id)
    {
        $article = $this->articleFacade->getById($id);
        if ($this->termsAndConditionsFacade->isArticleUsedAsTermsAndConditions($article)) {
            $message = t(
                'Article "%name%" set for displaying terms and conditions. This setting will be lost. Do you really want to delete it?',
                ['%name%' => $article->getName()]
            );
        } elseif ($this->cookiesFacade->isArticleUsedAsCookiesInfo($article)) {
            $message = t(
                'Article "%name%" set for displaying cookies information. This setting will be lost. Do you really want to delete it?',
                ['%name%' => $article->getName()]
            );
        } else {
            $message = t('Do you really want to remove this article?');
        }

        return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_article_delete', $id);
    }

    /**
     * @Route("/article/save-ordering/", condition="request.isXmlHttpRequest()")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function saveOrderingAction(Request $request)
    {
        $this->articleFacade->saveOrdering($request->get('rowIdsByGridId'));

        $responseData = ['success' => true];

        return new JsonResponse($responseData);
    }

    /**
     * @param string $articlePlacement
     * @return \Shopsys\ShopBundle\Component\Grid\Grid
     */
    private function getGrid($articlePlacement)
    {
        $queryBuilder = $this->articleFacade->getOrderedArticlesByDomainIdAndPlacementQueryBuilder(
            $this->selectedDomain->getId(),
            $articlePlacement
        );

        $dataSource = new QueryBuilderDataSource($queryBuilder, 'a.id');

        $gridId = $articlePlacement;
        $grid = $this->gridFactory->create($gridId, $dataSource);
        $grid->setDefaultOrder('position');

        $grid->addColumn('name', 'a.name', t('Name'));

        $grid->setActionColumnClassAttribute('table-col table-col-10');
        $grid->addEditActionColumn('admin_article_edit', ['id' => 'a.id']);
        $grid->addDeleteActionColumn('admin_article_deleteconfirm', ['id' => 'a.id'])
            ->setAjaxConfirm();

        $grid->enableMultipleDragAndDrop();
        $grid->setTheme('@ShopsysShop/Admin/Content/Article/listGrid.html.twig');

        return $grid;
    }
}
