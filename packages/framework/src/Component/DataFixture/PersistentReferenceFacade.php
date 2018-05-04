<?php

namespace Shopsys\FrameworkBundle\Component\DataFixture;

use Doctrine\ORM\EntityManagerInterface;

class PersistentReferenceFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceRepository
     */
    protected $persistentReferenceRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFactoryInterface
     */
    protected $persistentReferenceFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceRepository $persistentReferenceRepository
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFactoryInterface $persistentReferenceFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        PersistentReferenceRepository $persistentReferenceRepository,
        PersistentReferenceFactoryInterface $persistentReferenceFactory
    ) {
        $this->em = $em;
        $this->persistentReferenceRepository = $persistentReferenceRepository;
        $this->persistentReferenceFactory = $persistentReferenceFactory;
    }

    /**
     * @param string $name
     * @return object
     */
    public function getReference($name)
    {
        $persistentReference = $this->persistentReferenceRepository->getByReferenceName($name);
        $entity = $this->em->find($persistentReference->getEntityName(), $persistentReference->getEntityId());

        if ($entity === null) {
            throw new \Shopsys\FrameworkBundle\Component\DataFixture\Exception\EntityNotFoundException($name);
        }

        return $entity;
    }

    /**
     * @param string $name
     * @param object $object
     */
    public function persistReference($name, $object)
    {
        if (!is_object($object)) {
            throw new \Shopsys\FrameworkBundle\Component\DataFixture\Exception\ObjectRequiredException($object);
        }

        $entityName = get_class($object);

        if (method_exists($object, 'getId')) {
            $objectId = $object->getId();

            if ($objectId === null) {
                throw new \Shopsys\FrameworkBundle\Component\DataFixture\Exception\EntityIdIsNotSetException($name, $object);
            }

            try {
                $persistentReference = $this->persistentReferenceRepository->getByReferenceName($name);
                $persistentReference->replace($entityName, $objectId);
            } catch (\Shopsys\FrameworkBundle\Component\DataFixture\Exception\PersistentReferenceNotFoundException $ex) {
                $persistentReference = $this->persistentReferenceFactory->create($name, $entityName, $objectId);
                $this->em->persist($persistentReference);
            }
            $this->em->flush($persistentReference);
        } else {
            $message = 'Entity "' . $entityName . '" does not have a method "getId", which is necessary for persistent references.';
            throw new \Shopsys\FrameworkBundle\Component\DataFixture\Exception\MethodGetIdDoesNotExistException($message);
        }
    }
}
