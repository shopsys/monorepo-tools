<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *   name="friendly_urls",
 *   indexes={
 *		@ORM\Index(columns={"route_name", "entity_id"})
 *   }
 * )
 * @ORM\Entity
 */
class FriendlyUrl {

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 */
	private $routeName;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 */
	private $entityId;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 */
	private $domainId;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 * @ORM\Id
	 */
	private $slug;

	/**
	 * @param string $routeName
	 * @param int $entityId
	 * @param int $domainId
	 * @param string $slug
	 */
	public function __construct($routeName, $entityId, $domainId, $slug) {
		$this->routeName = $routeName;
		$this->entityId = $entityId;
		$this->domainId = $domainId;
		$this->slug = $slug;
	}

	/**
	 * @return string
	 */
	public function getRouteName() {
		return $this->routeName;
	}

	/**
	 * @return integer
	 */
	public function getEntityId() {
		return $this->entityId;
	}

	/**
	 * @return string
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

}
