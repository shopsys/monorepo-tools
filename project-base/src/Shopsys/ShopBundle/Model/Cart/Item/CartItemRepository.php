<?php

namespace Shopsys\ShopBundle\Model\Cart\Item;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Shopsys\ShopBundle\Model\Customer\CustomerIdentifier;

class CartItemRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getCartItemRepository()
    {
        return $this->em->getRepository(CartItem::class);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @return \Shopsys\ShopBundle\Model\Cart\Item\CartItem[]
     */
    public function getAllByCustomerIdentifier(CustomerIdentifier $customerIdentifier)
    {
        $criteria = [];
        if ($customerIdentifier->getUser() !== null) {
            $criteria['user'] = $customerIdentifier->getUser()->getId();
        } else {
            $criteria['cartIdentifier'] = $customerIdentifier->getCartIdentifier();
        }

        return $this->getCartItemRepository()->findBy($criteria, ['id' => 'desc']);
    }

    /**
     * @param int $daysLimit
     */
    public function deleteOldCartsForUnregisteredCustomers($daysLimit)
    {
        $nativeQuery = $this->em->createNativeQuery(
            'DELETE FROM cart_items WHERE cart_identifier NOT IN (
                SELECT CI.cart_identifier
                FROM cart_items CI
                WHERE CI.added_at > :timeLimit
            ) AND user_id IS NULL',
            new ResultSetMapping()
        );

        $nativeQuery->execute([
            'timeLimit' => new DateTime('-' . $daysLimit . ' days'),
        ]);
    }

    /**
     * @param int $daysLimit
     */
    public function deleteOldCartsForRegisteredCustomers($daysLimit)
    {
        $nativeQuery = $this->em->createNativeQuery(
            'DELETE FROM cart_items WHERE user_id NOT IN (
                SELECT CI.user_id
                FROM cart_items CI
                WHERE CI.added_at > :timeLimit
            ) AND user_id IS NOT NULL',
            new ResultSetMapping()
        );

        $nativeQuery->execute([
            'timeLimit' => new DateTime('-' . $daysLimit . ' days'),
        ]);
    }
}
