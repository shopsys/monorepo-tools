<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\String\DatabaseSearching;
use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;

class UserRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getUserRepository()
    {
        return $this->em->getRepository(User::class);
    }

    /**
     * @param string $email
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Customer\User|null
     */
    public function findUserByEmailAndDomain($email, $domainId)
    {
        return $this->getUserRepository()->findOneBy([
            'email' => mb_strtolower($email),
            'domainId' => $domainId,
        ]);
    }

    /**
     * @param string $email
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Customer\User|null
     */
    public function getUserByEmailAndDomain($email, $domainId)
    {
        $user = $this->findUserByEmailAndDomain($email, $domainId);

        if ($user === null) {
            throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\UserNotFoundByEmailAndDomainException(
                $email,
                $domainId
            );
        }

        return $user;
    }

    /**
     * @param int $id
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    public function getUserById($id)
    {
        $user = $this->findById($id);
        if ($user === null) {
            throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\UserNotFoundException($id);
        }
        return $user;
    }

    /**
     * @param int $id
     * @return \Shopsys\FrameworkBundle\Model\Customer\User|null
     */
    public function findById($id)
    {
        return $this->getUserRepository()->find($id);
    }

    /**
     * @param int $id
     * @param string $loginToken
     * @return \Shopsys\FrameworkBundle\Model\Customer\User|null
     */
    public function findByIdAndLoginToken($id, $loginToken)
    {
        return $this->getUserRepository()->findOneBy([
            'id' => $id,
            'loginToken' => $loginToken,
        ]);
    }

    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData $quickSearchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getCustomerListQueryBuilderByQuickSearchData(
        $domainId,
        QuickSearchFormData $quickSearchData
    ) {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('
                u.id,
                u.email,
                MAX(pg.name) AS pricingGroup,
                MAX(ba.city) city,
                MAX(ba.telephone) telephone,
                MAX(CASE WHEN ba.companyCustomer = true
                        THEN ba.companyName
                        ELSE CONCAT(u.lastName, \' \', u.firstName)
                    END) AS name,
                COUNT(o.id) ordersCount,
                SUM(o.totalPriceWithVat) ordersSumPrice,
                MAX(o.createdAt) lastOrderAt')
            ->from(User::class, 'u')
            ->where('u.domainId = :selectedDomainId')
            ->setParameter('selectedDomainId', $domainId)
            ->join('u.billingAddress', 'ba')
            ->leftJoin(Order::class, 'o', 'WITH', 'o.customer = u.id AND o.deleted = :deleted')
            ->setParameter('deleted', false)
            ->leftJoin(PricingGroup::class, 'pg', 'WITH', 'pg.id = u.pricingGroup')
            ->groupBy('u.id');

        if ($quickSearchData->text !== null && $quickSearchData->text !== '') {
            $queryBuilder
                ->andWhere('
                    (
                        NORMALIZE(u.lastName) LIKE NORMALIZE(:text)
                        OR
                        NORMALIZE(u.email) LIKE NORMALIZE(:text)
                        OR
                        NORMALIZE(ba.companyName) LIKE NORMALIZE(:text)
                        OR
                        NORMALIZE(ba.telephone) LIKE :text
                    )');
            $querySearchText = DatabaseSearching::getFullTextLikeSearchString($quickSearchData->text);
            $queryBuilder->setParameter('text', $querySearchText);
        }

        return $queryBuilder;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $oldPricingGroup
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $newPricingGroup
     */
    public function replaceUsersPricingGroup(PricingGroup $oldPricingGroup, PricingGroup $newPricingGroup)
    {
        $this->em->createQueryBuilder()
            ->update(User::class, 'u')
            ->set('u.pricingGroup', ':newPricingGroup')->setParameter('newPricingGroup', $newPricingGroup)
            ->where('u.pricingGroup = :oldPricingGroup')->setParameter('oldPricingGroup', $oldPricingGroup)
            ->getQuery()->execute();
    }
}
