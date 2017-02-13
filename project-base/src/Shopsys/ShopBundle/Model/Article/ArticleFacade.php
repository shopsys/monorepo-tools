<?php

namespace Shopsys\ShopBundle\Model\Article;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\ShopBundle\Model\Article\ArticleRepository;

class ArticleFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticleRepository
     */
    private $articleRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\ShopBundle\Model\Article\ArticleRepository $articleRepository
     * @param \Shopsys\ShopBundle\Component\Domain\Domain
     * @param \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    public function __construct(
        EntityManager $em,
        ArticleRepository $articleRepository,
        Domain $domain,
        FriendlyUrlFacade $friendlyUrlFacade
    ) {
        $this->em = $em;
        $this->articleRepository = $articleRepository;
        $this->domain = $domain;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
    }

    /**
     * @param int $articleId
     * @return \Shopsys\ShopBundle\Model\Article\Article|null
     */
    public function findById($articleId)
    {
        return $this->articleRepository->findById($articleId);
    }

    /**
     * @param int $articleId
     * @return \Shopsys\ShopBundle\Model\Article\Article
     */
    public function getById($articleId)
    {
        return $this->articleRepository->getById($articleId);
    }

    /**
     * @param int $articleId
     * @return \Shopsys\ShopBundle\Model\Article\Article
     */
    public function getVisibleById($articleId)
    {
        return $this->articleRepository->getVisibleById($articleId);
    }

    /**
     * @param $domainId
     * @return int
     */
    public function getAllArticlesCountByDomainId($domainId)
    {
        return $this->articleRepository->getAllArticlesCountByDomainId($domainId);
    }

    /**
     * @param string $placement
     * @return \Shopsys\ShopBundle\Model\Article\Article[]
     */
    public function getVisibleArticlesForPlacementOnCurrentDomain($placement)
    {
        return $this->articleRepository->getVisibleArticlesForPlacement($this->domain->getId(), $placement);
    }

    /**
     * @param int $domainId
     * @param string $placement
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrderedArticlesByDomainIdAndPlacementQueryBuilder($domainId, $placement)
    {
        return $this->articleRepository->getOrderedArticlesByDomainIdAndPlacementQueryBuilder($domainId, $placement);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Article\ArticleData $articleData
     * @return \Shopsys\ShopBundle\Model\Article\Article
     */
    public function create(ArticleData $articleData)
    {
        $article = new Article($articleData);

        $this->em->persist($article);
        $this->em->flush();
        $this->friendlyUrlFacade->createFriendlyUrlForDomain(
            'front_article_detail',
            $article->getId(),
            $article->getName(),
            $article->getDomainId()
        );
        $this->em->flush();

        return $article;
    }

    /**
     * @param int $articleId
     * @param \Shopsys\ShopBundle\Model\Article\ArticleData $articleData
     * @return \Shopsys\ShopBundle\Model\Article\Article
     */
    public function edit($articleId, ArticleData $articleData)
    {
        $article = $this->articleRepository->getById($articleId);
        $article->edit($articleData);

        $this->friendlyUrlFacade->saveUrlListFormData('front_article_detail', $article->getId(), $articleData->urls);
        $this->friendlyUrlFacade->createFriendlyUrlForDomain(
            'front_article_detail',
            $article->getId(),
            $article->getName(),
            $article->getDomainId()
        );
        $this->em->flush();

        return $article;
    }

    /**
     * @param int $articleId
     */
    public function delete($articleId)
    {
        $article = $this->articleRepository->getById($articleId);

        $this->em->remove($article);
        $this->em->flush();
    }

    /**
     * @param string[gridId][] $rowIdsByGridId
     */
    public function saveOrdering(array $rowIdsByGridId)
    {
        foreach ($rowIdsByGridId as $gridId => $rowIds) {
            foreach ($rowIds as $position => $rowId) {
                $article = $this->articleRepository->findById($rowId);
                $article->setPosition($position);
                $article->setPlacement($gridId);
            }
        }
        $this->em->flush();
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Article\Article[]
     */
    public function getAllByDomainId($domainId)
    {
        return $this->articleRepository->getAllByDomainId($domainId);
    }
}
