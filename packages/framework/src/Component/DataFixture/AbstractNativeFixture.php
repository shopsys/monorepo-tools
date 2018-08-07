<?php

namespace Shopsys\FrameworkBundle\Component\DataFixture;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

abstract class AbstractNativeFixture extends AbstractFixture
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @required
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function autowireEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $sql
     * @param array|null $parameters
     * @return mixed
     */
    protected function executeNativeQuery($sql, array $parameters = null)
    {
        $nativeQuery = $this->entityManager->createNativeQuery($sql, new ResultSetMapping());
        return $nativeQuery->execute($parameters);
    }
}
