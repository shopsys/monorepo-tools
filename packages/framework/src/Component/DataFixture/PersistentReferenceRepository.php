<?php

namespace Shopsys\FrameworkBundle\Component\DataFixture;

use Doctrine\ORM\EntityManager;

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
     * @return \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReference|null
     */
    public function findByReferenceName($referenceName)
    {
        return $this->getReferenceRepository()->find(['referenceName' => $referenceName]);
    }

    /**
     * @param string $referenceName
     * @return \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReference
     */
    public function getByReferenceName($referenceName)
    {
        $reference = $this->findByReferenceName($referenceName);
        if ($reference === null) {
            throw new \Shopsys\FrameworkBundle\Component\DataFixture\Exception\PersistentReferenceNotFoundException($referenceName);
        }
        return $reference;
    }
}
