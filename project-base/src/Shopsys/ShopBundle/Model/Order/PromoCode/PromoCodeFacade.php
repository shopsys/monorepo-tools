<?php

namespace Shopsys\ShopBundle\Model\Order\PromoCode;

use Doctrine\ORM\EntityManager;

class PromoCodeFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeRepository
     */
    private $promoCodeRepository;

    public function __construct(EntityManager $em, PromoCodeRepository $promoCodeRepository)
    {
        $this->em = $em;
        $this->promoCodeRepository = $promoCodeRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     * @return \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode
     */
    public function create(PromoCodeData $promoCodeData)
    {
        $promoCode = new PromoCode($promoCodeData);
        $this->em->persist($promoCode);
        $this->em->flush();

        return $promoCode;
    }

    /**
     * @param int $promoCodeId
     * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
     * @return \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode
     */
    public function edit($promoCodeId, PromoCodeData $promoCodeData)
    {
        $promoCode = $this->getById($promoCodeId);
        $promoCode->edit($promoCodeData);
        $this->em->flush();

        return $promoCode;
    }

    /**
     * @param int $promoCodeId
     * @return \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode
     */
    public function getById($promoCodeId)
    {
        return $this->promoCodeRepository->getById($promoCodeId);
    }

    /**
     * @param int $promoCodeId
     */
    public function deleteById($promoCodeId)
    {
        $promoCode = $this->getById($promoCodeId);
        $this->em->remove($promoCode);
        $this->em->flush();
    }

    /**
     * @param string $code
     * @return \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode|null
     */
    public function findPromoCodeByCode($code)
    {
        return $this->promoCodeRepository->findByCode($code);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode[]
     */
    public function getAll()
    {
        return $this->promoCodeRepository->getAll();
    }
}
