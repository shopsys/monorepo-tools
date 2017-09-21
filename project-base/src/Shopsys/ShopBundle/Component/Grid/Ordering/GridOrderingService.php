<?php

namespace Shopsys\ShopBundle\Component\Grid\Ordering;

class GridOrderingService
{
    /**
     * @param \Shopsys\ShopBundle\Component\Grid\Ordering\OrderableEntityInterface|null $entity
     * @param int $position
     */
    public function setPosition($entity, $position)
    {
        if ($entity instanceof OrderableEntityInterface) {
            $entity->setPosition($position);
        } else {
            throw new \Shopsys\ShopBundle\Component\Grid\Ordering\Exception\EntityIsNotOrderableException();
        }
    }
}
