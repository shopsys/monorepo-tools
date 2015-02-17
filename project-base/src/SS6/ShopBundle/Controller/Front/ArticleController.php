<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Model\Article\ArticleEditFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticleController extends Controller {

	/**
	 * @param int $id
	 */
	public function detailAction($id) {
		$articleEditFacade = $this->get(ArticleEditFacade::class);
		/* @var $articleEditFacade \SS6\ShopBundle\Model\Article\ArticleEditFacade */

		$article = $articleEditFacade->getById($id);

		return $this->render('@SS6Shop/Front/Content/Article/detail.html.twig', [
			'article' => $article,
		]);
	}

	public function menuAction() {
		$articleEditFacade = $this->get(ArticleEditFacade::class);
		/* @var $articleEditFacade \SS6\ShopBundle\Model\Article\ArticleEditFacade */

		$articles = $articleEditFacade->getArticlesForMenuOnCurrentDomain();

		return $this->render('@SS6Shop/Front/Content/Article/menu.html.twig', [
			'articles' => $articles,
		]);
	}

}
