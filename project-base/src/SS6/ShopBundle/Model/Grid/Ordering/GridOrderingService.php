<?php

namespace SS6\ShopBundle\Model\Grid\Ordering;

use SS6\ShopBundle\Model\Grid\Ordering\OrderableEntityInterface;

class GridOrderingService {

	/**
	 * @param \SS6\ShopBundle\Model\Grid\Ordering\OrderableEntityInterface|null $entity
	 * @param int $position
	 */
	public function setPosition($entity, $position) {
		if ($entity instanceof OrderableEntityInterface) {
			$entity->setPosition($position);
		} else {
			throw new \SS6\ShopBundle\Model\Grid\Ordering\Exception\EntityIsNotOrderableException();
		}
	}

}
