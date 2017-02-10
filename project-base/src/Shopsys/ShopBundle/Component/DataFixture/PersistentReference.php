<?php

namespace Shopsys\ShopBundle\Component\DataFixture;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="persistent_references")
 * @ORM\Entity
 */
class PersistentReference
{

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     * @ORM\Id
     */
    private $referenceName;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $entityName;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $entityId;

    /**
     * @param string $referenceName
     * @param string $entityName
     * @param int $entityId
     */
    public function __construct($referenceName, $entityName, $entityId) {
        $this->referenceName = $referenceName;
        $this->entityName = $entityName;
        $this->entityId = $entityId;
    }

    /**
     * @param string $entityName
     * @param int $entityId
     */
    public function replace($entityName, $entityId) {
        $this->entityName = $entityName;
        $this->entityId = $entityId;
    }

    /**
     * @return string
     */
    public function getReferenceName() {
        return $this->referenceName;
    }

    /**
     * @return string
     */
    public function getEntityName() {
        return $this->entityName;
    }

    /**
     * @return int
     */
    public function getEntityId() {
        return $this->entityId;
    }
}
