<?php

namespace Tests\ShopBundle\Database\Model\Article;

use DateTime;
use Shopsys\FrameworkBundle\Model\Article\ArticleDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Article\ArticleFactoryInterface;
use Shopsys\ShopBundle\Model\Article\Article;
use Tests\ShopBundle\Test\DatabaseTestCase;

class ArticleTest extends DatabaseTestCase
{
    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticleDataFactory
     */
    private $articleDataFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Article\ArticleFactory
     */
    private $articleFactory;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        parent::setUp();
        $this->articleDataFactory = $this->getContainer()->get(ArticleDataFactoryInterface::class);
        $this->articleFactory = $this->getContainer()->get(ArticleFactoryInterface::class);
        $this->em = $this->getEntityManager();
    }

    public function testArticleIsCorrectlyRestoredFromDatabase()
    {
        $articleData = $this->articleDataFactory->create();

        $articleData->name = 'Demonstrative name';
        $articleData->placement = 'topMenu';
        $articleData->seoTitle = 'Demonstrative seo title';
        $articleData->seoMetaDescription = 'Demonstrative seo description';
        $articleData->seoH1 = 'Demonstrative seo H1';
        $articleData->createdAt = new DateTime('2000-01-01T01:01:01');

        $article = $this->articleFactory->create($articleData);

        $this->em->persist($article);
        $this->em->flush();

        $articleId = $article->getId();

        $this->em->clear();

        $refreshedArticle = $this->em->getRepository(Article::class)->find($articleId);

        $this->assertSame('Demonstrative name', $refreshedArticle->getName());
        $this->assertSame('topMenu', $refreshedArticle->getPlacement());
        $this->assertSame('Demonstrative seo title', $refreshedArticle->getSeoTitle());
        $this->assertSame('Demonstrative seo description', $refreshedArticle->getSeoMetaDescription());
        $this->assertSame('Demonstrative seo H1', $refreshedArticle->getSeoH1());
        $this->assertEquals(new DateTime('2000-01-01T01:01:01'), $refreshedArticle->getCreatedAt());
    }
}
