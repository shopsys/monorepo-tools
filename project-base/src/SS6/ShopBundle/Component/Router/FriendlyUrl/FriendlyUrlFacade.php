<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository;
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
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository
	 */
	private $friendlyUrlRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		EntityManager $em,
		DomainRouterFactory $domainRouterFactory,
		FriendlyUrlService $friendlyUrlService,
		FriendlyUrlRepository $friendlyUrlRepository,
		Domain $domain
	) {
		$this->em = $em;
		$this->domainRouterFactory = $domainRouterFactory;
		$this->friendlyUrlService = $friendlyUrlService;
		$this->friendlyUrlRepository = $friendlyUrlRepository;
		$this->domain = $domain;
	}

	/**
	 * @param string $routeName
	 * @param int $entityId
	 * @param string[locale] $namesByLocale
	 */
	public function createFriendlyUrls($routeName, $entityId, array $namesByLocale) {
		$friendlyUrls = $this->friendlyUrlService->createFriendlyUrls($routeName, $entityId, $namesByLocale);
		foreach ($friendlyUrls as $friendlyUrl) {
			$locale = $this->domain->getDomainConfigById($friendlyUrl->getDomainId())->getLocale();
			$this->resolveUniquenessOfFriendlyUrlAndFlush($friendlyUrl, $namesByLocale[$locale]);
		}
	}

	/**
	 * @param string $routeName
	 * @param int $entityId
	 * @param string $entityName
	 * @param int $domainId
	 */
	public function createFriendlyUrlForDomain($routeName, $entityId, $entityName, $domainId) {
		$friendlyUrl = $this->friendlyUrlService->createFriendlyUrlIfValid($routeName, $entityId, $entityName, $domainId);
		if ($friendlyUrl !== null) {
			$this->resolveUniquenessOfFriendlyUrlAndFlush($friendlyUrl, $entityName);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
	 * @param string $entityName
	 */
	private function resolveUniquenessOfFriendlyUrlAndFlush(FriendlyUrl $friendlyUrl, $entityName) {
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
				$matchedRouteData
			);
			$friendlyUrl = $friendlyUrlUniqueResult->getFriendlyUrlForPersist();
		} while (!$friendlyUrlUniqueResult->isUnique());

		if ($friendlyUrl !== null) {
			$this->em->persist($friendlyUrl);
			$this->em->flush($friendlyUrl);
		}
	}

	/**
	 * @param string $routeName
	 * @param int $entityId
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
	 */
	public function getAllByRouteNameAndEntityId($routeName, $entityId) {
		return $this->friendlyUrlRepository->getAllByRouteNameAndEntityId($routeName, $entityId);
	}

	/**
	 * @param \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[][] $urlListFormData
	 */
	public function saveUrlListFormData(array $urlListFormData) {
		foreach ($urlListFormData['toDelete'] as $friendlyUrls) {
			foreach ($friendlyUrls as $friendlyUrl) {
				$this->em->remove($friendlyUrl);
			}
		}

		$this->em->flush();
	}

}
