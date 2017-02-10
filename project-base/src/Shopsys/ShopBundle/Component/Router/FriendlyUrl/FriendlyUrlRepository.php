<?php

namespace Shopsys\ShopBundle\Component\Router\FriendlyUrl;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl;

class FriendlyUrlRepository
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getFriendlyUrlRepository() {
        return $this->em->getRepository(FriendlyUrl::class);
    }

    /**
     * @param int $domainId
     * @param string $slug
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl|null
     */
    public function findByDomainIdAndSlug($domainId, $slug) {
        return $this->getFriendlyUrlRepository()->findOneBy(
            [
                'domainId' => $domainId,
                'slug' => $slug,
            ]
        );
    }

    /**
     * @param int $domainId
     * @param string $routeName
     * @param int $entityId
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl
     */
    public function getMainFriendlyUrl($domainId, $routeName, $entityId) {
        $criteria = [
            'domainId' => $domainId,
            'routeName' => $routeName,
            'entityId' => $entityId,
            'main' => true,
        ];
        $friendlyUrl = $this->getFriendlyUrlRepository()->findOneBy($criteria);

        if ($friendlyUrl === null) {
            throw new \Shopsys\ShopBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlNotFoundException();
        }

        return $friendlyUrl;
    }

    /**
     * @param int $domainId
     * @param string $routeName
     * @param int $entityId
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl|null
     */
    public function findMainFriendlyUrl($domainId, $routeName, $entityId) {
        $criteria = [
            'domainId' => $domainId,
            'routeName' => $routeName,
            'entityId' => $entityId,
            'main' => true,
        ];

        return $this->getFriendlyUrlRepository()->findOneBy($criteria);
    }

    /**
     * @param string $routeName
     * @param int $entityId
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
     */
    public function getAllByRouteNameAndEntityId($routeName, $entityId) {
        $criteria = [
            'routeName' => $routeName,
            'entityId' => $entityId,
        ];

        return $this->getFriendlyUrlRepository()->findBy(
            $criteria, [
                'domainId' => 'ASC',
                'slug' => 'ASC',
            ]
        );
    }

    /**
     * @param string $routeName
     * @param int $entityId
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
     */
    public function getAllByRouteNameAndEntityIdAndDomainId($routeName, $entityId, $domainId) {
        $criteria = [
            'routeName' => $routeName,
            'entityId' => $entityId,
            'domainId' => $domainId,
        ];

        return $this->getFriendlyUrlRepository()->findBy($criteria);
    }

    /**
     * @param object[] $entities
     * @param string $routeName
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
     */
    public function getMainFriendlyUrlsByEntitiesIndexedByEntityId(array $entities, $routeName, $domainId) {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('f')
            ->from(FriendlyUrl::class, 'f', 'f.entityId')
            ->andWhere('f.routeName = :routeName')->setParameter('routeName', $routeName)
            ->andWhere('f.entityId IN (:entities)')->setParameter('entities', $entities)
            ->andWhere('f.domainId = :domainId')->setParameter('domainId', $domainId)
            ->andWhere('f.main = TRUE');

        return $queryBuilder->getQuery()->execute();
    }
}
