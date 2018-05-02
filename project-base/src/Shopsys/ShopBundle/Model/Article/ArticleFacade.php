<?php

namespace Shopsys\ShopBundle\Model\Article;

use Shopsys\FrameworkBundle\Model\Article\ArticleData;
use Shopsys\FrameworkBundle\Model\Article\ArticleFacade as BaseArticleFacade;

class ArticleFacade extends BaseArticleFacade
{
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
}
