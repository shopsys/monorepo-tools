<?php

namespace SS6\ShopBundle\Model\Article;

use Doctrine\ORM\EntityManager;

class ArticleRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getArticleRepository() {
		return $this->em->getRepository(Article::class);
	}

	/**
	 * @param string $articleId
	 * @return \SS6\ShopBundle\Model\Article\Article
	 */
	public function findById($articleId) {
		return $this->getArticleRepository()->find($articleId);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Article\Article[]
	 */
	public function getArticlesForMenu() {
		return $this->getArticleRepository()->findAll();
	}

	/**
	 * @param int $articleId
	 * @return \SS6\ShopBundle\Model\Article\Article
	 * @throws ArticleNotFoundException
	 */
	public function getById($articleId) {
		$criteria = array('id' => $articleId);
		$user = $this->getArticleRepository()->findOneBy($criteria);
		if ($user === null) {
			throw new \SS6\ShopBundle\Model\Article\Exception\ArticleNotFoundException($criteria);
		}
		return $user;
	}

}
