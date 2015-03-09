<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Controller\Front\BaseController;
use SS6\ShopBundle\Model\Article\ArticleEditFacade;

class ArticleController extends BaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleEditFacade
	 */
	private $articleEditFacade;

	public function __construct(ArticleEditFacade $articleEditFacade) {
		$this->articleEditFacade = $articleEditFacade;
	}

	/**
	 * @param int $id
	 */
	public function detailAction($id) {
		$article = $this->articleEditFacade->getById($id);

		return $this->render('@SS6Shop/Front/Content/Article/detail.html.twig', [
			'article' => $article,
		]);
	}

	public function menuAction() {
		$articles = $this->articleEditFacade->getArticlesForMenuOnCurrentDomain();

		return $this->render('@SS6Shop/Front/Content/Article/menu.html.twig', [
			'articles' => $articles,
		]);
	}

}
