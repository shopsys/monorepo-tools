<?php

namespace Shopsys\ShopBundle\Model\Article;

use Shopsys\FrameworkBundle\Model\Article\Article as BaseArticle;
use Shopsys\FrameworkBundle\Model\Article\ArticleDataFactory as BaseArticleDataFactory;

class ArticleDataFactory extends BaseArticleDataFactory
{
    /**
     * @param \Shopsys\ShopBundle\Model\Article\Article $article
     * @return \Shopsys\ShopBundle\Model\Article\ArticleData
     */
    public function createFromArticle(BaseArticle $article)
    {
        $articleData = new ArticleData();
        $articleData->setFromEntity($article);

        foreach ($this->domain->getAll() as $domainConfig) {
            $articleData->urls->mainFriendlyUrlsByDomainId[$domainConfig->getId()] =
                $this->friendlyUrlFacade->findMainFriendlyUrl(
                    $domainConfig->getId(),
                    'front_article_detail',
                    $article->getId()
                );
        }

        return $articleData;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Article\ArticleData
     */
    public function createDefault()
    {
        $articleData = new ArticleData();
        $articleData->domainId = $this->adminDomainTabsFacade->getSelectedDomainId();

        return $articleData;
    }
}
