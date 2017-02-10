<?php

namespace Shopsys\ShopBundle\Component\Router\FriendlyUrl;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="friendly_urls",
 *     indexes={
 *         @ORM\Index(columns={"route_name", "entity_id"})
 *     }
 * )
 * @ORM\Entity
 */
class FriendlyUrl
{

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $routeName;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $entityId;

    /**
     * @var int
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
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $main;

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
        $this->main = false;
    }

    /**
     * @return string
     */
    public function getRouteName() {
        return $this->routeName;
    }

    /**
     * @return int
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

    /**
     * @return bool
     */
    public function isMain() {
        return $this->main;
    }

    /**
     * @param bool $main
     */
    public function setMain($main) {
        $this->main = $main;
    }
}
