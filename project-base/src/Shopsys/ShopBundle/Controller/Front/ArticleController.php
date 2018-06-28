<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Model\Article\Article;
use Shopsys\FrameworkBundle\Model\Article\ArticleFacade;

class ArticleController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleFacade
     */
    private $articleFacade;

    public function __construct(ArticleFacade $articleFacade)
    {
        $this->articleFacade = $articleFacade;
    }

    /**
     * @param int $id
     */
    public function detailAction($id)
    {
        $article = $this->articleFacade->getVisibleById($id);

        return $this->render('@ShopsysShop/Front/Content/Article/detail.html.twig', [
            'article' => $article,
        ]);
    }

    public function menuAction()
    {
        $articles = $this->articleFacade->getVisibleArticlesForPlacementOnCurrentDomain(Article::PLACEMENT_TOP_MENU);

        return $this->render('@ShopsysShop/Front/Content/Article/menu.html.twig', [
            'articles' => $articles,
        ]);
    }

    public function footerAction()
    {
        $articles = $this->articleFacade->getVisibleArticlesForPlacementOnCurrentDomain(Article::PLACEMENT_FOOTER);

        return $this->render('@ShopsysShop/Front/Content/Article/menu.html.twig', [
            'articles' => $articles,
        ]);
    }
}
