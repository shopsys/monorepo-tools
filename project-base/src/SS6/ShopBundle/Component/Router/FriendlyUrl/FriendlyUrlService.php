<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use SS6\ShopBundle\Component\String\TransformString;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;

class FriendlyUrlService {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

	/**
	 * @param string $routeName
	 * @param int $entityId
	 * @param string[] $namesByLocale
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
	 */
	public function createFriendlyUrls($routeName, $entityId, array $namesByLocale) {
		$friendlyUrls = [];

		foreach ($this->domain->getAll() as $domainConfig) {
			$friendlyUrl = $this->createFriendlyUrlIfValid($routeName, $entityId, $namesByLocale, $domainConfig);
			if ($friendlyUrl !== null) {
				$friendlyUrls[] = $friendlyUrl;
			}
		}

		return $friendlyUrls;
	}

	/**
	 * @param int $attempt
	 * @param \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
	 * @param string[locale] $namesByLocale
	 * @param array|null $matchedRouteData
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlUniqueResult
	 */
	public function getFriendlyUrlUniqueResult(
		$attempt,
		FriendlyUrl $friendlyUrl,
		array $namesByLocale,
		array $matchedRouteData = null
	) {
		if ($matchedRouteData === null) {
			return new FriendlyUrlUniqueResult(true, $friendlyUrl);
		}

		if ($friendlyUrl->getRouteName() === $matchedRouteData['_route']
			&& $friendlyUrl->getEntityId() === $matchedRouteData['id']
		) {
			return new FriendlyUrlUniqueResult(true, null);
		}

		$newIndexedFriendlyUrl = $this->createFriendlyUrlIfValid(
			$friendlyUrl->getRouteName(),
			$friendlyUrl->getEntityId(),
			$namesByLocale,
			$this->domain->getDomainConfigById($friendlyUrl->getDomainId()),
			$attempt + 1 // if URL is duplicate, try again with "url-2", "url-3" and so on
		);

		return new FriendlyUrlUniqueResult(false, $newIndexedFriendlyUrl);
	}

	/**
	 * @param string $routeName
	 * @param int $entityId
	 * @param string[] $namesByLocale
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param int|null $indexPostfix
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl|null
	 */
	private function createFriendlyUrlIfValid(
		$routeName,
		$entityId,
		array $namesByLocale,
		DomainConfig $domainConfig,
		$indexPostfix = null
	) {
		foreach ($namesByLocale as $locale => $name) {
			if ($name !== null
				&& $name !== ''
				&& $domainConfig->getLocale() === $locale
			) {
				$nameForUrl = $name . ($name === null ? '' : '-' . $indexPostfix);
				$slug = TransformString::stringToFriendlyUrlSlug($nameForUrl);

				return new FriendlyUrl($routeName, $entityId, $domainConfig->getId(), $slug);
			}
		}

		return null;
	}
}
