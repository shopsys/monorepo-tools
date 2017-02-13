<?php

namespace Shopsys\ShopBundle\Model\Article;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Domain\SelectedDomain;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;

class ArticleDataFactory
{
    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\SelectedDomain
     */
    private $selectedDomain;

    public function __construct(
        FriendlyUrlFacade $friendlyUrlFacade,
        Domain $domain,
        SelectedDomain $selectedDomain
    ) {
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->domain = $domain;
        $this->selectedDomain = $selectedDomain;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Article\Article $article
     * @return \Shopsys\ShopBundle\Model\Article\ArticleData
     */
    public function createFromArticle(Article $article)
    {
        $articleData = new ArticleData();
        $articleData->setFromEntity($article);

        foreach ($this->domain->getAll() as $domainConfig) {
            $articleData->urls->mainOnDomains[$domainConfig->getId()] =
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
        $articleData->domainId = $this->selectedDomain->getId();

        return $articleData;
    }
}
