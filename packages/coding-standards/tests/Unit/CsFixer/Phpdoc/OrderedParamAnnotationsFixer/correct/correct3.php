<?php

class SomeClass
{
    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string $class
     * @param string $alias
     * @param string $condition
     * @return \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    public function addOrExtendJoin(QueryBuilder $queryBuilder, $class, $alias, $condition)
    {
    }
}
