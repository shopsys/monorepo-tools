<?php

namespace Shopsys\FrameworkBundle\Component\Router;

use Psr\Log\LoggerInterface;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter;
use Symfony\Cmf\Component\Routing\ChainRouter;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class DomainRouter extends ChainRouter
{
    /**
     * @var bool
     */
    protected $freeze = false;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter
     */
    protected $friendlyUrlRouter;

    /**
     * @param \Symfony\Component\Routing\RequestContext $context
     * @param \Symfony\Component\Routing\RouterInterface $basicRouter
     * @param \Symfony\Component\Routing\RouterInterface $localizedRouter
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter $friendlyUrlRouter
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(
        RequestContext $context,
        RouterInterface $basicRouter,
        RouterInterface $localizedRouter,
        FriendlyUrlRouter $friendlyUrlRouter,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($logger);
        $this->setContext($context);
        $this->freeze = true;
        $this->friendlyUrlRouter = $friendlyUrlRouter;

        $this->add($basicRouter, 10);
        $this->add($localizedRouter, 20);
        $this->add($friendlyUrlRouter, 30);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
     * @param array $parameters
     * @param int $referenceType
     * @return string
     */
    public function generateByFriendlyUrl(FriendlyUrl $friendlyUrl, array $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        return $this->friendlyUrlRouter->generateByFriendlyUrl($friendlyUrl, $parameters, $referenceType);
    }

    /**
     * @param \Symfony\Component\Routing\RequestContext $context
     */
    public function setContext(RequestContext $context)
    {
        if ($this->freeze) {
            $message = 'Set context is not supported in chain DomainRouter';
            throw new \Shopsys\FrameworkBundle\Component\Router\Exception\NotSupportedException($message);
        }

        parent::setContext($context);
    }
}
