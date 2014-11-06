<?php

namespace SS6\ShopBundle\Model\Grid\Ordering;

use SS6\ShopBundle\Model\Grid\Ordering\OrderingEntityInterface;

class GridOrderingService {

	const ENTITY_NAME_PREFIX = 'SS6\ShopBundle\Model\\';
	const ENTITY_NAME_PREFIX_REPLACED = 'grid.';

	/**
	 * @param string $entityClass
	 * @return string
	 */
	public function getEntityName($entityClass) {
		return preg_replace('/^' . preg_quote(self::ENTITY_NAME_PREFIX) . '/u', self::ENTITY_NAME_PREFIX_REPLACED, $entityClass);
	}

	/**
	 * @param string $entityName
	 * @return string
	 */
	public function getEntityClass($entityName) {
		if (strpos($entityName, 'grid') === 0) {
			return preg_replace('/^' . preg_quote(self::ENTITY_NAME_PREFIX_REPLACED) . '/u', self::ENTITY_NAME_PREFIX, $entityName);
		}
		return $entityName;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Grid\Ordering\OrderingEntityInterface|null $entity
	 * @param int $position
	 */
	public function setPosition($entity, $position) {
		if ($entity instanceof OrderingEntityInterface) {
			$entity->setPosition($position);
		} else {
			throw new \SS6\ShopBundle\Model\Grid\Ordering\Exception\OrderingEntityNotSupportException();
		}
	}

}
