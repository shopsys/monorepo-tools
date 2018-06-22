<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Twig_Environment;

class FeedRendererFactory
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedInterface $feed
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedRenderer
     */
    public function create(FeedInterface $feed): FeedRenderer
    {
        $templateFilepath = $feed->getTemplateFilepath();
        $twigTemplate = $this->twig->load($templateFilepath);

        return new FeedRenderer($this->twig, $twigTemplate);
    }
}
