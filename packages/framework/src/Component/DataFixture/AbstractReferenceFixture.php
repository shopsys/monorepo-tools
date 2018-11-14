<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\DataFixture;

use Doctrine\Common\DataFixtures\FixtureInterface;

abstract class AbstractReferenceFixture implements FixtureInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade
     */
    private $persistentReferenceFacade;

    /**
     * @required
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     */
    public function autowirePersistentReferenceFacade(PersistentReferenceFacade $persistentReferenceFacade)
    {
        $this->persistentReferenceFacade = $persistentReferenceFacade;
    }

    /**
     * @param string $name
     * @param object $object
     */
    public function addReference($name, $object)
    {
        $this->persistentReferenceFacade->persistReference($name, $object);
    }

    /**
     * @param string $name
     * @return object
     */
    public function getReference($name)
    {
        return $this->persistentReferenceFacade->getReference($name);
    }

    /**
     * @param string $name
     * @param object $object
     * @param int $domainId
     */
    public function addReferenceForDomain(string $name, $object, int $domainId): void
    {
        $this->persistentReferenceFacade->persistReferenceForDomain($name, $object, $domainId);
    }

    /**
     * @param string $name
     * @param int $domainId
     * @return object
     */
    public function getReferenceForDomain(string $name, int $domainId)
    {
        return $this->persistentReferenceFacade->getReferenceForDomain($name, $domainId);
    }
}
