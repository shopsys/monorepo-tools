<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use SS6\ShopBundle\Component\String\TransformString;

class FriendlyUrlService {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

	/**
	 * @param string $routeName
	 * @param int $entityId
	 * @param string[locale] $namesByLocale
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
	 */
	public function createFriendlyUrls($routeName, $entityId, $namesByLocale) {
		$friendlyUrls = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			if (array_key_exists($domainConfig->getLocale(), $namesByLocale)) {
				$friendlyUrl = $this->createFriendlyUrlIfValid(
					$routeName,
					$entityId,
					$namesByLocale[$domainConfig->getLocale()],
					$domainConfig->getId()
				);

				if ($friendlyUrl !== null) {
					$friendlyUrls[] = $friendlyUrl;
				}
			}
		}

		return $friendlyUrls;
	}

	/**
	 * @param int $attempt
	 * @param \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
	 * @param string $entityName
	 * @param array|null $matchedRouteData
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlUniqueResult
	 */
	public function getFriendlyUrlUniqueResult(
		$attempt,
		FriendlyUrl $friendlyUrl,
		$entityName,
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
			$entityName,
			$friendlyUrl->getDomainId(),
			$attempt + 1 // if URL is duplicate, try again with "url-2", "url-3" and so on
		);

		return new FriendlyUrlUniqueResult(false, $newIndexedFriendlyUrl);
	}

	/**
	 * @param string $routeName
	 * @param int $entityId
	 * @param string $entityName
	 * @param int $domainId
	 * @param int|null $indexPostfix
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl|null
	 */
	public function createFriendlyUrlIfValid(
		$routeName,
		$entityId,
		$entityName,
		$domainId,
		$indexPostfix = null
	) {
		if ($entityName !== null
			&& $entityName !== ''
		) {
			$nameForUrl = $entityName . ($entityName === null ? '' : '-' . $indexPostfix);
			$slug = TransformString::stringToFriendlyUrlSlug($nameForUrl) . '/';

			return new FriendlyUrl($routeName, $entityId, $domainId, $slug);
		}

		return null;
	}

	/**
	 * @param \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
	 * @return string
	 */
	public function getAbsoluteUrlByFriendlyUrl(FriendlyUrl $friendlyUrl) {
		$domainConfig = $this->domain->getDomainConfigById($friendlyUrl->getDomainId());

		return $domainConfig->getUrl() . '/' . $friendlyUrl->getSlug();
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param string $slug
	 * @return string
	 */
	public function getAbsoluteUrlByDomainConfigAndSlug(DomainConfig $domainConfig, $slug) {
		return $domainConfig->getUrl() . '/' . $slug;
	}

}
