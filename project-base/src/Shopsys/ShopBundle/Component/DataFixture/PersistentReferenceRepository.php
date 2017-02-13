<?php

namespace Shopsys\ShopBundle\Component\DataFixture;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\DataFixture\PersistentReference;

class PersistentReferenceRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getReferenceRepository()
    {
        return $this->em->getRepository(PersistentReference::class);
    }

    /**
     * @param string $referenceName
     * @return \Shopsys\ShopBundle\Component\DataFixture\PersistentReference|null
     */
    public function findByReferenceName($referenceName)
    {
        return $this->getReferenceRepository()->find(['referenceName' => $referenceName]);
    }

    /**
     * @param string $referenceName
     * @return \Shopsys\ShopBundle\Component\DataFixture\PersistentReference
     */
    public function getByReferenceName($referenceName)
    {
        $reference = $this->findByReferenceName($referenceName);
        if ($reference === null) {
            throw new \Shopsys\ShopBundle\Component\DataFixture\Exception\PersistentReferenceNotFoundException($referenceName);
        }
        return $reference;
    }
}
