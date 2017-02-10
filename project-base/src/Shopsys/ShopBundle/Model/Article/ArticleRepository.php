<?php

namespace Shopsys\ShopBundle\Model\Article;

use Doctrine\ORM\EntityManager;

class ArticleRepository
{
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
     * @return \Shopsys\ShopBundle\Model\Article\Article|null
     */
    public function findById($articleId) {
        return $this->getArticleRepository()->find($articleId);
    }

    /**
     * @param int $domainId
     * @param string $placement
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrderedArticlesByDomainIdAndPlacementQueryBuilder($domainId, $placement) {
        return $this->getArticlesByDomainIdQueryBuilder($domainId)
            ->andWhere('a.placement = :placement')->setParameter('placement', $placement)
            ->orderBy('a.position, a.id');
    }

    /**
     * @param int $domainId
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getArticlesByDomainIdQueryBuilder($domainId) {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Article::class, 'a')
            ->where('a.domainId = :domainId')->setParameter('domainId', $domainId);
    }

    /**
     * @param int $domainId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getVisibleArticlesByDomainIdQueryBuilder($domainId) {
        return $this->getAllVisibleQueryBuilder()
            ->andWhere('a.domainId = :domainId')
            ->setParameter('domainId', $domainId);
    }

    /**
     * @param $domainId
     * @return int
     */
    public function getAllArticlesCountByDomainId($domainId) {
        return (int)($this->getArticlesByDomainIdQueryBuilder($domainId)
            ->select('COUNT(a)')
            ->getQuery()->getSingleScalarResult());
    }

    /**
     * @param int $domainId
     * @param string $placement
     * @return \Shopsys\ShopBundle\Model\Article\Article[]
     */
    public function getVisibleArticlesForPlacement($domainId, $placement) {
        $queryBuilder = $this->getVisibleArticlesByDomainIdQueryBuilder($domainId)
            ->andWhere('a.placement = :placement')->setParameter('placement', $placement)
            ->orderBy('a.position, a.id');

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param int $articleId
     * @return \Shopsys\ShopBundle\Model\Article\Article
     */
    public function getById($articleId) {
        $article = $this->getArticleRepository()->find($articleId);
        if ($article === null) {
            $message = 'Article with ID ' . $articleId . ' not found';
            throw new \Shopsys\ShopBundle\Model\Article\Exception\ArticleNotFoundException($message);
        }
        return $article;
    }

    /**
     * @param int $articleId
     * @return \Shopsys\ShopBundle\Model\Article\Article
     */
    public function getVisibleById($articleId) {
        $article = $this->getAllVisibleQueryBuilder()
            ->andWhere('a.id = :articleId')
            ->setParameter('articleId', $articleId)
            ->getQuery()->getOneOrNullResult();

        if ($article === null) {
            $message = 'Article with ID ' . $articleId . ' not found';
            throw new \Shopsys\ShopBundle\Model\Article\Exception\ArticleNotFoundException($message);
        }
        return $article;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getAllVisibleQueryBuilder() {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Article::class, 'a')
            ->where('a.hidden = false');
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Article\Article[]
     */
    public function getAllByDomainId($domainId) {
        return $this->getArticleRepository()->findBy([
            'domainId' => $domainId,
        ]);
    }
}
