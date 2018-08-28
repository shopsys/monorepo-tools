<?php

namespace Tests\ShopBundle\Unit\Model\Article;

use DateTime;
use PHPUnit\Framework\TestCase;
use Shopsys\ShopBundle\Model\Article\Article;
use Shopsys\ShopBundle\Model\Article\ArticleData;

class ArticleTest extends TestCase
{
    public function testValidationOfArticleAttributeExtension()
    {
        $articleData = new ArticleData();
        $articleData->createdAt = new Datetime('2000-01-01');

        $article = new Article($articleData);

        $this->assertEquals(new Datetime('2000-01-01'), $article->getCreatedAt());
    }
}
