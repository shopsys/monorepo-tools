<?php

namespace SS6\ShopBundle\Component\Validator;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetadata;
use SS6\ShopBundle\Component\Validator\Auto;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Exception\MappingException;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;

class AutoValidatorAnnotationLoader implements LoaderInterface {

	/**
	 * @var \Doctrine\Common\Annotations\Reader
	 */
	protected $reader;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @param \Doctrine\Common\Annotations\Reader $reader
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(
		Reader $reader,
		EntityManager $em
	) {
		$this->reader = $reader;
		$this->em = $em;
	}

	/**
	 * @param \Symfony\Component\Validator\Mapping\ClassMetadata $classMetadata
	 * @return boolean
	 * @throws MappingException
	 */
	public function loadClassMetadata(ClassMetadata $classMetadata) {
		$loaded = false;

		$reflClass = $classMetadata->getReflectionClass();

		foreach ($this->reader->getClassAnnotations($reflClass) as $annotation) {
			if ($annotation instanceof Auto) {
				$this->processClassAnnotation($classMetadata, $annotation);
			}

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * @param \Symfony\Component\Validator\Mapping\ClassMetadata $classMetadata
	 * @param \SS6\ShopBundle\Component\Validator\Auto $annotation
	 */
	private function processClassAnnotation(ClassMetadata $classMetadata, Auto $annotation) {
		$classProperties = $classMetadata->getReflectionClass()->getProperties();

		$entityMetadata = $this->em->getClassMetadata($annotation->getEntity());

		foreach ($classProperties as $property) {
			/* @var $property \ReflectionProperty */
			$propertyName = $property->getName();

			$constraints = array();
			if ($entityMetadata->hasField($propertyName)) {
				$constraints = $this->resolveConstraintsForField($entityMetadata, $propertyName);
			} elseif ($entityMetadata->hasAssociation($propertyName)) {
				$constraints = $this->resolveConstraintsForAssociation($entityMetadata, $propertyName);
			}

			foreach ($constraints as $constraint) {
				$classMetadata->addPropertyConstraint($propertyName, $constraint);
			}
		}
	}

	/**
	 * @param \Doctrine\ORM\Mapping\ClassMetadata $entityMetadata
	 * @param string $fieldName
	 * @return \Symfony\Component\Validator\Constraint[]
	 */
	private function resolveConstraintsForField(DoctrineClassMetadata $entityMetadata, $fieldName) {
		$constraints = array();

		$fieldMapping = $entityMetadata->getFieldMapping($fieldName);

		if (!$fieldMapping['nullable']) {
			$constraints[] = new Constraints\NotBlank();
		}

		if (in_array($fieldMapping['type'], array('string', 'text'))) {
			if ($fieldMapping['length'] !== null) {
				$constraints[] = new Constraints\Length(array('max' => $fieldMapping['length']));
			}
		}

		if ($fieldMapping['type'] === 'date') {
			$constraints[] = new Constraints\Date();
		}

		if (in_array($fieldMapping['type'], array('datetime', 'datetimetz'))) {
			$constraints[] = new Constraints\DateTime();
		}

		return $constraints;
	}

	/**
	 * @param \Doctrine\ORM\Mapping\ClassMetadata $entityMetadata
	 * @param string $fieldName
	 * @return \Symfony\Component\Validator\Constraint[]
	 */
	private function resolveConstraintsForAssociation(DoctrineClassMetadata $entityMetadata, $fieldName) {
		$fieldMapping = $entityMetadata->getAssociationMapping($fieldName);

		return array();
	}

}
