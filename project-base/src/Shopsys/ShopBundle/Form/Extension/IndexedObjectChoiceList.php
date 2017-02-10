<?php

namespace Shopsys\ShopBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class IndexedObjectChoiceList extends ObjectChoiceList {

	/**
	 * @var string
	 */
	private $indexPath;

	/**
	 * @var PropertyAccessorInterface
	 */
	private $propertyAccessor;

	/**
	 * @param array|\Traversable $choices
	 * @param string $indexPath
	 * @param string|null $labelPath
	 * @param array $preferredChoices
	 * @param string|null $groupPath
	 * @param string|null $valuePath
	 * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface|null $propertyAccessor
	 */
	public function __construct(
		$choices,
		$indexPath,
		$labelPath = null,
		array $preferredChoices = [],
		$groupPath = null,
		$valuePath = null,
		PropertyAccessorInterface $propertyAccessor = null
	) {
		$this->indexPath = $indexPath;
		$this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();

		parent::__construct($choices, $labelPath, $preferredChoices, $groupPath, $valuePath, $this->propertyAccessor);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function createIndex($choice) {
		return $this->propertyAccessor->getValue($choice, $this->indexPath);
	}

}
