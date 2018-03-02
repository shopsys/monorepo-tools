<?php

namespace Shopsys\FrameworkBundle\Model\Article;

use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;

class ArticleDataFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    private $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    public function __construct(
        FriendlyUrlFacade $friendlyUrlFacade,
        Domain $domain,
        AdminDomainTabsFacade $adminDomainTabsFacade
    ) {
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->domain = $domain;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\Article $article
     * @return \Shopsys\FrameworkBundle\Model\Article\ArticleData
     */
    public function createFromArticle(Article $article)
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
     * @return \Shopsys\FrameworkBundle\Model\Article\ArticleData
     */
    public function createDefault()
    {
        $articleData = new ArticleData();
        $articleData->domainId = $this->adminDomainTabsFacade->getSelectedDomainId();

        return $articleData;
    }
}
