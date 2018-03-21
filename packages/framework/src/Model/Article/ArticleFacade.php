<?php

namespace Shopsys\FrameworkBundle\Model\Article;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;

class ArticleFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Article\ArticleRepository
     */
    private $articleRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Article\ArticleRepository $articleRepository
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    public function __construct(
        EntityManagerInterface $em,
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
     * @return \Shopsys\FrameworkBundle\Model\Article\Article|null
     */
    public function findById($articleId)
    {
        return $this->articleRepository->findById($articleId);
    }

    /**
     * @param int $articleId
     * @return \Shopsys\FrameworkBundle\Model\Article\Article
     */
    public function getById($articleId)
    {
        return $this->articleRepository->getById($articleId);
    }

    /**
     * @param int $articleId
     * @return \Shopsys\FrameworkBundle\Model\Article\Article
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
     * @return \Shopsys\FrameworkBundle\Model\Article\Article[]
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
     * @param \Shopsys\FrameworkBundle\Model\Article\ArticleData $articleData
     * @return \Shopsys\FrameworkBundle\Model\Article\Article
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
     * @param \Shopsys\FrameworkBundle\Model\Article\ArticleData $articleData
     * @return \Shopsys\FrameworkBundle\Model\Article\Article
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
     * @param string[][] $rowIdsByGridId
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
     * @return \Shopsys\FrameworkBundle\Model\Article\Article[]
     */
    public function getAllByDomainId($domainId)
    {
        return $this->articleRepository->getAllByDomainId($domainId);
    }
}
