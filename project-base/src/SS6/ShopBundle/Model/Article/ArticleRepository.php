<?php

namespace SS6\ShopBundle\Model\Article;

use Doctrine\ORM\EntityManager;

class ArticleRepository {

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $entityRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->entityRepository = $entityManager->getRepository(Article::class);
	}

	/**
	 * @param string $articleId
	 * @return \SS6\ShopBundle\Model\Article\Article
	 */
	public function findById($articleId) {
		return $this->entityRepository->find($articleId);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Article\Article[]
	 */
	public function getArticlesForMenu() {
		return $this->entityRepository->findAll();
	}

	/**
	 * @param int $articleId
	 * @return \SS6\ShopBundle\Model\Article\Article
	 * @throws ArticleNotFoundException
	 */
	public function getById($articleId) {
		$criteria = array('id' => $articleId);
		$user = $this->entityRepository->findOneBy($criteria);
		if ($user === null) {
			throw new \SS6\ShopBundle\Model\Article\Exception\ArticleNotFoundException($criteria);
		}
		return $user;
	}

}
