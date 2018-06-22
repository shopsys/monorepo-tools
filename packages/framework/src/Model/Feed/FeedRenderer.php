<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Twig_Environment;
use Twig_TemplateWrapper;

class FeedRenderer
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Twig_TemplateWrapper
     */
    private $template;

    public function __construct(Twig_Environment $twig, Twig_TemplateWrapper $template)
    {
        $this->twig = $twig;
        $this->template = $template;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    public function renderBegin(DomainConfig $domainConfig): string
    {
        return $this->getRenderedBlock('begin', ['domainConfig' => $domainConfig]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    public function renderEnd(DomainConfig $domainConfig): string
    {
        return $this->getRenderedBlock('end', ['domainConfig' => $domainConfig]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedItemInterface $item
     * @return string
     */
    public function renderItem(DomainConfig $domainConfig, FeedItemInterface $item): string
    {
        return $this->getRenderedBlock('item', ['item' => $item, 'domainConfig' => $domainConfig]);
    }

    /**
     * @param string $name
     * @param array $parameters
     * @return string
     */
    private function getRenderedBlock(string $name, array $parameters): string
    {
        if (!$this->template->hasBlock($name)) {
            throw new \Shopsys\FrameworkBundle\Model\Feed\Exception\TemplateBlockNotFoundException($name, $this->template->getTemplateName());
        }

        $templateParameters = $this->twig->mergeGlobals($parameters);

        return $this->template->renderBlock($name, $templateParameters);
    }
}
