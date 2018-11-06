<?php

namespace Shopsys\FrameworkBundle\Component\EntityExtension;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder as BaseQueryBuilder;

class QueryBuilder extends BaseQueryBuilder
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    private $entityNameResolver;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityManagerInterface $em, EntityNameResolver $entityNameResolver)
    {
        parent::__construct($em);

        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function add($dqlPartName, $dqlPart, $append = false)
    {
        $resolvedDqlPart = $this->entityNameResolver->resolveIn($dqlPart);

        return parent::add($dqlPartName, $resolvedDqlPart, $append);
    }
}
