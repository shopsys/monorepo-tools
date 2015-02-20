<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository;
use SS6\ShopBundle\Model\Domain\Domain;

class FriendlyUrlFacade {

	const MAX_URL_UNIQUE_RESOLVE_ATTEMPT = 100;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository
	 */
	private $friendlyUrlRespository;

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
		FriendlyUrlRepository $friendlyUrlRespository,
		FriendlyUrlService $friendlyUrlService,
		Domain $domain
	) {
		$this->em = $em;
		$this->friendlyUrlRespository = $friendlyUrlRespository;
		$this->friendlyUrlService = $friendlyUrlService;
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
			$this->resolveUniquenessOfFriendlyUrlAndFlush($friendlyUrl, $namesByLocale);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
	 * @param string[locale] $namesByLocale
	 */
	private function resolveUniquenessOfFriendlyUrlAndFlush(FriendlyUrl $friendlyUrl, array $namesByLocale) {
		$attempt = 0;
		do {
			$attempt++;
			if ($attempt > self::MAX_URL_UNIQUE_RESOLVE_ATTEMPT) {
				throw new \SS6\ShopBundle\Component\Router\FriendlyUrl\Exception\ReachMaxUrlUniqueResolveAttemptException(
					$friendlyUrl,
					$attempt
				);
			}

			$duplicateFriendlyUrlOnSameDomain = $this->friendlyUrlRespository->findByDomainIdAndUrl(
				$friendlyUrl->getDomainId(),
				$friendlyUrl->getUrl()
			);
			$friendlyUrlUniqueResult = $this->friendlyUrlService->getFriendlyUrlUniqueResult(
				$attempt,
				$namesByLocale,
				$friendlyUrl,
				$duplicateFriendlyUrlOnSameDomain
			);
			$friendlyUrl = $friendlyUrlUniqueResult->getFriendlyUrlForPersist();
		} while (!$friendlyUrlUniqueResult->isUnique());

		if ($friendlyUrl !== null) {
			$this->em->persist($friendlyUrl);
			$this->em->flush($friendlyUrl);
		}
	}

}
