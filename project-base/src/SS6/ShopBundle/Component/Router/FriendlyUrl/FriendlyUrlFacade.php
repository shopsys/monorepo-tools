<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Model\Domain\Domain;

class FriendlyUrlFacade {

	const MAX_URL_UNIQUE_RESOLVE_ATTEMPT = 100;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlService
	 */
	private $friendlyUrlService;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		EntityManager $em,
		DomainRouterFactory $domainRouterFactory,
		FriendlyUrlService $friendlyUrlService,
		Domain $domain
	) {
		$this->em = $em;
		$this->domainRouterFactory = $domainRouterFactory;
		$this->friendlyUrlService = $friendlyUrlService;
		$this->domain = $domain;
	}

	/**
	 * @param string $routeName
	 * @param int $entityId
	 * @param string[locale] $namesByLocale
	 */
	public function createFriendlyUrls($routeName, $entityId, array $namesByLocale) {
		foreach ($this->domain->getAll() as $domainConfig) {
			if (key_exists($domainConfig->getLocale(), $namesByLocale)) {
				$this->createFriendlyUrlForDomain(
					$routeName,
					$entityId,
					$namesByLocale[$domainConfig->getLocale()],
					$domainConfig->getId()
				);
			}
		}
	}

	/**
	 * @param string $routeName
	 * @param int $entityId
	 * @param string $entityName
	 * @param int $domainId
	 */
	public function createFriendlyUrlForDomain($routeName, $entityId, $entityName, $domainId) {
		$friendlyUrl = $this->friendlyUrlService->createFriendlyUrl($routeName, $entityId, $entityName, $domainId);
		if ($friendlyUrl !== null) {
			$this->resolveUniquenessOfFriendlyUrlAndFlush($friendlyUrl, $entityName, $domainId);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
	 * @param string $entityName
	 * @param int $domainId
	 */
	private function resolveUniquenessOfFriendlyUrlAndFlush(FriendlyUrl $friendlyUrl, $entityName, $domainId) {
		$attempt = 0;
		do {
			$attempt++;
			if ($attempt > self::MAX_URL_UNIQUE_RESOLVE_ATTEMPT) {
				throw new \SS6\ShopBundle\Component\Router\FriendlyUrl\Exception\ReachMaxUrlUniqueResolveAttemptException(
					$friendlyUrl,
					$attempt
				);
			}

			$domainRouter = $this->domainRouterFactory->getRouter($friendlyUrl->getDomainId());
			try {
				$matchedRouteData = $domainRouter->match('/' . $friendlyUrl->getSlug() . '/');
			} catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
				$matchedRouteData = null;
			}

			$friendlyUrlUniqueResult = $this->friendlyUrlService->getFriendlyUrlUniqueResult(
				$attempt,
				$friendlyUrl,
				$entityName,
				$domainId,
				$matchedRouteData
			);
			$friendlyUrl = $friendlyUrlUniqueResult->getFriendlyUrlForPersist();
		} while (!$friendlyUrlUniqueResult->isUnique());

		if ($friendlyUrl !== null) {
			$this->em->persist($friendlyUrl);
			$this->em->flush($friendlyUrl);
		}
	}

}
