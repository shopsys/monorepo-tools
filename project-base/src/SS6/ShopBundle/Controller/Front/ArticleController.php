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

		try {
			$article = $articleRepository->getById($id);
		} catch (\SS6\ShopBundle\Model\Article\Exception\ArticleNotFoundException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Article not found', $e);
		}

		return $this->render('@SS6Shop/Front/Content/Article/detail.html.twig', array(
			'article' => $article,
		));
	}

}
