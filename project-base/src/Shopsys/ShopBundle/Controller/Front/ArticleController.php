<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Model\Article\ArticleEditFacade;
use Shopsys\ShopBundle\Model\Article\ArticlePlacementList;

class ArticleController extends FrontBaseController {

	/**
	 * @var \Shopsys\ShopBundle\Model\Article\ArticleEditFacade
	 */
	private $articleEditFacade;

	public function __construct(ArticleEditFacade $articleEditFacade) {
		$this->articleEditFacade = $articleEditFacade;
	}

	/**
	 * @param int $id
	 */
	public function detailAction($id) {
		$article = $this->articleEditFacade->getVisibleById($id);

		return $this->render('@ShopsysShop/Front/Content/Article/detail.html.twig', [
			'article' => $article,
		]);
	}

	public function menuAction() {
		$articles = $this->articleEditFacade->getVisibleArticlesForPlacementOnCurrentDomain(ArticlePlacementList::PLACEMENT_TOP_MENU);

		return $this->render('@ShopsysShop/Front/Content/Article/menu.html.twig', [
			'articles' => $articles,
		]);
	}

	public function footerAction() {
		$articles = $this->articleEditFacade->getVisibleArticlesForPlacementOnCurrentDomain(ArticlePlacementList::PLACEMENT_FOOTER);

		return $this->render('@ShopsysShop/Front/Content/Article/menu.html.twig', [
			'articles' => $articles,
		]);
	}

}
