<?php

namespace Shopsys\ShopBundle\Model\Order\PromoCode;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode;

class PromoCodeRepository
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
    private function getPromoCodeRepository()
    {
        return $this->em->getRepository(PromoCode::class);
    }

    /**
     * @param int $promoCodeId
     * @return \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode|null
     */
    public function findById($promoCodeId)
    {
        return $this->getPromoCodeRepository()->find($promoCodeId);
    }

    /**
     * @param string $code
     * @return \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode|null
     */
    public function findByCode($code)
    {
        return $this->getPromoCodeRepository()->findOneBy(['code' => $code]);
    }

    /**
     * @param int $promoCodeId
     * @return \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode
     */
    public function getById($promoCodeId)
    {
        $promoCode = $this->findById($promoCodeId);

        if ($promoCode === null) {
            throw new \Shopsys\ShopBundle\Model\Order\PromoCode\Exception\PromoCodeNotFoundException(
                'Promo code with ID ' . $promoCodeId . ' not found.'
            );
        }

        return $promoCode;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode[]
     */
    public function getAll()
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('pc')
            ->from(PromoCode::class, 'pc');

        return $queryBuilder->getQuery()->execute();
    }
}
