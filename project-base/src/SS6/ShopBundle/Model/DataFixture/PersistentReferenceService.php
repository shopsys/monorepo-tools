<?php

namespace SS6\ShopBundle\Model\DataFixture;

use Doctrine\ORM\EntityManager;
use SS6\Environment;
use SS6\ShopBundle\Model\DataFixture\PersistentReferenceRepository;
use Symfony\Component\HttpKernel\Kernel;

class PersistentReferenceService {

	/**
	 * @var \Symfony\Component\HttpKernel\Kernel
	 */
	private $kernel;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;
	
	/**
	 * @var \SS6\ShopBundle\Model\DataFixture\PersistentReferenceRepository
	 */
	private $persistentReferenceRepository;

	/**
	 * @var \SS6\ShopBundle\Model\DataFixture\PersistentReference
	 */
	private $persistentReferencesByName = [];

	/**
	 * @param \Symfony\Component\HttpKernel\Kernel $kernel
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\DataFixture\PersistentReferenceRepository $persistentReferenceRepository
	 */
	public function __construct(Kernel $kernel, EntityManager $em, PersistentReferenceRepository $persistentReferenceRepository) {
		$this->kernel = $kernel;
		$this->em = $em;
		$this->persistentReferenceRepository = $persistentReferenceRepository;
	}

	/**
	 * @param string $name
	 * @return object
	 * @throws SS6\ShopBundle\Model\DataFixture\Exception\EntityNotFoundException
	 */
	public function getReference($name) {
		$persistentReference = $this->persistentReferenceRepository->get($name);
		$entity = $this->em->find($persistentReference->getEntityName(), $persistentReference->getEntityId());
		
		if ($entity === null) {
			throw new \SS6\ShopBundle\Model\DataFixture\Exception\EntityNotFoundException($name);
		}

		return $entity;
	}

	/**
	 * @param string $name
	 * @param object $object
	 * @throws \SS6\ShopBundle\Model\DataFixture\Exception\MethodGetIdDoesNotExistException
	 */
	public function persistReference($name, $object) {
		if ($this->kernel->getEnvironment() !== Environment::ENVIRONMENT_TEST) {
			return;
		}
		
		$entityName = get_class($object);

		if (method_exists($object, 'getId')) {
			$this->checkCleanPersistentReferences();

			// must persist and flush object befor persist reference because object id does not exists
			$this->em->persist($object);
			$this->em->flush();
			if (array_key_exists($name, $this->persistentReferencesByName)) {
				$this->persistentReferencesByName[$name]->replace($entityName, $object->getId());
			} else {
				$persistentReference = new PersistentReference($name, $entityName, $object->getId());
				$this->persistentReferencesByName[$name] = $persistentReference;
				$this->em->persist($persistentReference);
			}
			$this->em->flush();
		} else {
			$message = 'Entity "' . $entityName . '" does not have a method "getId", which is necessary for persistent references.';
			throw new \SS6\ShopBundle\Model\DataFixture\Exception\MethodGetIdDoesNotExistException($message);
		}
	}

	private function checkCleanPersistentReferences() {
		if (count($this->persistentReferencesByName) === 0) {
			$this->persistentReferenceRepository->deleteAll();
		}
	}

}
