<?php

namespace SS6\ShopBundle\Component\Router;

use Psr\Log\LoggerInterface;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter;
use Symfony\Cmf\Component\Routing\ChainRouter;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class DomainRouter extends ChainRouter {

	/**
	 * @var bool
	 */
	private $freeze = false;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter
	 */
	private $friendlyUrlRouter;

	public function __construct(
		RequestContext $context,
		RouterInterface $basicRouter,
		RouterInterface $localizedRouter,
		FriendlyUrlRouter $friendlyUrlRouter,
		LoggerInterface $logger = null
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
	 * @param \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
	 * @param array $parameters
	 * @param string $referenceType
	 * @return string
	 */
	public function generateByFriendlyUrl(FriendlyUrl $friendlyUrl, $parameters = [], $referenceType = self::ABSOLUTE_PATH) {
		return $this->friendlyUrlRouter->generateByFriendlyUrl($friendlyUrl, $parameters, $referenceType);
	}

	public function setContext(RequestContext $context) {
		if ($this->freeze) {
			$message = 'Set context is not supported in chain DomainRouter';
			throw new \SS6\ShopBundle\Component\Router\Exception\NotSupportedException($message);
		}

		parent::setContext($context);
	}

}
