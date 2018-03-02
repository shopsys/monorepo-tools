<?php

namespace Shopsys\FrameworkBundle\Component\Grid\Ordering;

class GridOrderingService
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Grid\Ordering\OrderableEntityInterface|null $entity
     * @param int $position
     */
    public function setPosition($entity, $position)
    {
        if ($entity instanceof OrderableEntityInterface) {
            $entity->setPosition($position);
        } else {
            throw new \Shopsys\FrameworkBundle\Component\Grid\Ordering\Exception\EntityIsNotOrderableException();
        }
    }
}
