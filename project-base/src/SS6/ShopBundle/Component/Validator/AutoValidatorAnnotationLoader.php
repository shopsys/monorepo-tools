<?php

namespace SS6\ShopBundle\Component\Validator;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata as DoctrineClassMetadata;
use SS6\ShopBundle\Component\Validator\Auto;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;

class AutoValidatorAnnotationLoader implements LoaderInterface {

	const TRANSLATIONS_ASSOCIATION = 'translations';

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
			$constraints = $this->resolvePropertyConstraintsInEntity($propertyName, $entityMetadata);

			foreach ($constraints as $constraint) {
				$classMetadata->addPropertyConstraint($propertyName, $constraint);
			}
		}
	}

	/**
	 * @param type $propertyName
	 * @param \Doctrine\ORM\Mapping\ClassMetadata $entityMetadata
	 * @return \Symfony\Component\Validator\Constraint[]
	 */
	private function resolvePropertyConstraintsInEntity($propertyName, DoctrineClassMetadata $entityMetadata) {
		$constraints = array();
		if ($entityMetadata->hasField($propertyName)) {
			$constraints = $this->resolveConstraintsForEntityField($entityMetadata, $propertyName);
		} elseif ($entityMetadata->hasAssociation($propertyName)) {
			$constraints = $this->resolveConstraintsForEntityAssociation($entityMetadata, $propertyName);
		} elseif ($entityMetadata->hasAssociation(self::TRANSLATIONS_ASSOCIATION)) {
			$constraints = $this->resolveConstraintsForTranslationsEntityField($entityMetadata, $propertyName);
		}

		return $constraints;
	}

	/**
	 * @param \Doctrine\ORM\Mapping\ClassMetadata $entityMetadata
	 * @param string $fieldName
	 * @return \Symfony\Component\Validator\Constraint[]
	 */
	private function resolveConstraintsForEntityField(DoctrineClassMetadata $entityMetadata, $fieldName) {
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
	 * @param string $propertyName
	 * @return \Symfony\Component\Validator\Constraint[]
	 */
	private function resolveConstraintsForTranslationsEntityField(DoctrineClassMetadata $entityMetadata, $propertyName) {
		$fieldMapping = $entityMetadata->getAssociationMapping(self::TRANSLATIONS_ASSOCIATION);
		if (!isset($fieldMapping['targetEntity'])) {
			$message = 'At entity: ' . $entityMetadata->getName();
			throw new \SS6\ShopBundle\Component\Validator\Exception\TranslationsEntityNotSpecifiedException($message);
		}

		$translationsEntity = $fieldMapping['targetEntity'];
		$translationsEntityMetadata = $this->em->getClassMetadata($translationsEntity);

		$translationsConstraints = $this->resolvePropertyConstraintsInEntity($propertyName, $translationsEntityMetadata);
		if (count($translationsConstraints) > 0) {
			$constraints = array(
				new Constraints\All(array(
					'constraints' => $translationsConstraints,
				)),
			);
		} else {
			$constraints = array();
		}

		return $constraints;
	}

	/**
	 * @param \Doctrine\ORM\Mapping\ClassMetadata $entityMetadata
	 * @param string $fieldName
	 * @return \Symfony\Component\Validator\Constraint[]
	 */
	private function resolveConstraintsForEntityAssociation(DoctrineClassMetadata $entityMetadata, $fieldName) {
		$fieldMapping = $entityMetadata->getAssociationMapping($fieldName);

		return array();
	}

}
