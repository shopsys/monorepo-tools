<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticleController extends Controller {

	/**
	 * @param int $id
	 */
	public function detailAction($id) {
		$articleRepository = $this->get('ss6.shop.article.article_repository');
		/* @var $articleRepository \SS6\ShopBundle\Model\Article\ArticleRepository */

		$article = $articleRepository->getById($id);

		return $this->render('@SS6Shop/Front/Content/Article/detail.html.twig', array(
			'article' => $article,
		));
	}

	public function menuAction() {
		$articleRepository = $this->get('ss6.shop.article.article_repository');
		/* @var $articleRepository \SS6\ShopBundle\Model\Article\ArticleRepository */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

		$articles = $articleRepository->getArticlesForMenu($domain->getId());

		return $this->render('@SS6Shop/Front/Content/Article/menu.html.twig', array(
			'articles' => $articles,
		));
	}

}
