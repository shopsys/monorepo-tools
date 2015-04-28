<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;

class FriendlyUrlRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getFriendlyUrlRepository() {
		return $this->em->getRepository(FriendlyUrl::class);
	}

	/**
	 * @param int $domainId
	 * @param string $slug
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl|null
	 */
	public function findByDomainIdAndSlug($domainId, $slug) {
		return $this->getFriendlyUrlRepository()->findOneBy(
			[
				'domainId' => $domainId,
				'slug' => $slug,
			]
		);
	}

	/**
	 * @param int $domainId
	 * @param string $routeName
	 * @param int $entityId
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl
	 */
	public function getByDomainIdAndRouteNameAndEntityId($domainId, $routeName, $entityId) {
		$criteria = [
			'domainId' => $domainId,
			'routeName' => $routeName,
			'entityId' => $entityId,
		];
		$friendlyUrl = $this->getFriendlyUrlRepository()->findOneBy($criteria, ['slug' => 'ASC']);

		if ($friendlyUrl === null) {
			throw new \SS6\ShopBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlNotFoundException();
		}

		return $friendlyUrl;
	}

	/**
	 *
	 * @param string $routeName
	 * @param int $entityId
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
	 */
	public function getAllByRouteNameAndEntityId($routeName, $entityId) {
		$criteria = [
			'routeName' => $routeName,
			'entityId' => $entityId,
		];

		return $this->getFriendlyUrlRepository()->findBy($criteria);
	}

}
