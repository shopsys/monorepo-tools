<?php

namespace SS6\ShopBundle\Component\Router;

use Psr\Log\LoggerInterface;
use Symfony\Cmf\Component\Routing\ChainRouter;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class DomainRouter extends ChainRouter {

	/**
	 * @var bool
	 */
	private $freeze = false;

	public function __construct(
		RequestContext $context,
		RouterInterface $basicRouter,
		RouterInterface $localizedRouter,
		RouterInterface $friendlyUrlRouter,
		LoggerInterface $logger = null
	) {
		parent::__construct($logger);
		$this->setContext($context);
		$this->freeze = true;

		$this->add($basicRouter, 10);
		$this->add($localizedRouter, 20);
		$this->add($friendlyUrlRouter, 30);
	}

	public function setContext(RequestContext $context) {
		if ($this->freeze) {
			$message = 'Set context is not supported in chain DomainRouter';
			throw new \SS6\ShopBundle\Component\Router\Exception\NotSupportedException($message);
		}

		parent::setContext($context);
	}

}
