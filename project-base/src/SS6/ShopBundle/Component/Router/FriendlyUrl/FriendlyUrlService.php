<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;
use SS6\ShopBundle\Component\String\TransformString;
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
	 * @param string $entityName
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl|null
	 */
	public function createFriendlyUrl($routeName, $entityId, $entityName, $domainId) {
		return $this->createFriendlyUrlIfValid($routeName, $entityId, $entityName, $domainId);
	}

	/**
	 * @param int $attempt
	 * @param \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl $friendlyUrl
	 * @param string $entityName
	 * @param int $domainId
	 * @param array|null $matchedRouteData
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlUniqueResult
	 */
	public function getFriendlyUrlUniqueResult(
		$attempt,
		FriendlyUrl $friendlyUrl,
		$entityName,
		$domainId,
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
			$domainId,
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
	private function createFriendlyUrlIfValid(
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
			$slug = TransformString::stringToFriendlyUrlSlug($nameForUrl);

			return new FriendlyUrl($routeName, $entityId, $domainId, $slug);
		}

		return null;
	}
}
