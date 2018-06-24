<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Article\ArticleFormType;
use Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem;
use Shopsys\FrameworkBundle\Model\Article\Article;
use Shopsys\FrameworkBundle\Model\Article\ArticleDataFactory;
use Shopsys\FrameworkBundle\Model\Article\ArticleFacade;
use Shopsys\FrameworkBundle\Model\Cookies\CookiesFacade;
use Shopsys\FrameworkBundle\Model\LegalConditions\LegalConditionsFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleFacade
     */
    private $articleFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleDataFactory
     */
    private $articleDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
     */
    private $confirmDeleteResponseFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\LegalConditions\LegalConditionsFacade
     */
    private $legalConditionsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cookies\CookiesFacade
     */
    private $cookiesFacade;

    public function __construct(
        ArticleFacade $articleFacade,
        ArticleDataFactory $articleDataFactory,
        GridFactory $gridFactory,
        AdminDomainTabsFacade $adminDomainTabsFacade,
        Breadcrumb $breadcrumb,
        ConfirmDeleteResponseFactory $confirmDeleteResponseFactory,
        LegalConditionsFacade $legalConditionsFacade,
        CookiesFacade $cookiesFacade
    ) {
        $this->articleFacade = $articleFacade;
        $this->articleDataFactory = $articleDataFactory;
        $this->gridFactory = $gridFactory;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->breadcrumb = $breadcrumb;
        $this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
        $this->legalConditionsFacade = $legalConditionsFacade;
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
            'domain_id' => $this->adminDomainTabsFacade->getSelectedDomainId(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

        return $this->render('@ShopsysFramework/Admin/Content/Article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    /**
     * @Route("/article/list/")
     */
    public function listAction()
    {
        $gridTop = $this->getGrid(Article::PLACEMENT_TOP_MENU);
        $gridFooter = $this->getGrid(Article::PLACEMENT_FOOTER);
        $gridNone = $this->getGrid(Article::PLACEMENT_NONE);
        $articlesCountOnSelectedDomain = $this->articleFacade->getAllArticlesCountByDomainId($this->adminDomainTabsFacade->getSelectedDomainId());

        return $this->render('@ShopsysFramework/Admin/Content/Article/list.html.twig', [
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
            'domain_id' => $this->adminDomainTabsFacade->getSelectedDomainId(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

        return $this->render('@ShopsysFramework/Admin/Content/Article/new.html.twig', [
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
        } catch (\Shopsys\FrameworkBundle\Model\Article\Exception\ArticleNotFoundException $ex) {
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
        if ($this->legalConditionsFacade->isArticleUsedAsLegalConditions($article)) {
            $message = t(
                'Article "%name%" set for displaying legal conditions. This setting will be lost. Do you really want to delete it?',
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
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
     */
    private function getGrid($articlePlacement)
    {
        $queryBuilder = $this->articleFacade->getOrderedArticlesByDomainIdAndPlacementQueryBuilder(
            $this->adminDomainTabsFacade->getSelectedDomainId(),
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
        $grid->setTheme('@ShopsysFramework/Admin/Content/Article/listGrid.html.twig');

        return $grid;
    }
}
