<?php

namespace Shopsys\FrameworkBundle\Model\Article;

class ArticleFactory implements ArticleFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\ArticleData $data
     * @return \Shopsys\FrameworkBundle\Model\Article\Article
     */
    public function create(ArticleData $data): Article
    {
        return new Article($data);
    }
}
