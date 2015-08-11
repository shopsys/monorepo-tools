<?php

namespace SS6\ShopBundle\Model\Order\PromoCode;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeData;

class PromoCodeFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeRepository
	 */
	private $promoCodeRepository;

	public function __construct(EntityManager $em, PromoCodeRepository $promoCodeRepository) {
		$this->em = $em;
		$this->promoCodeRepository = $promoCodeRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
	 * @return \SS6\ShopBundle\Model\Order\PromoCode\PromoCode
	 */
	public function create(PromoCodeData $promoCodeData) {
		$promoCode = new PromoCode($promoCodeData);
		$this->em->persist($promoCode);
		$this->em->flush();

		return $promoCode;
	}

	/**
	 * @param int $promoCodeId
	 * @param \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
	 * @return \SS6\ShopBundle\Model\Order\PromoCode\PromoCode
	 */
	public function edit($promoCodeId, PromoCodeData $promoCodeData) {
		$promoCode = $this->getById($promoCodeId);
		$promoCode->edit($promoCodeData);
		$this->em->flush();

		return $promoCode;
	}

	/**
	 * @param int $promoCodeId
	 * @return \SS6\ShopBundle\Model\Order\PromoCode\PromoCode
	 */
	public function getById($promoCodeId) {
		return $this->promoCodeRepository->getById($promoCodeId);
	}

	/**
	 * @param int $promoCodeId
	 */
	public function deleteById($promoCodeId) {
		$promoCode = $this->getById($promoCodeId);
		$this->em->remove($promoCode);
		$this->em->flush();
	}

	/**
	 * @param string $code
	 * @return \SS6\ShopBundle\Model\Order\PromoCode\PromoCode|null
	 */
	public function findPromoCodeByCode($code) {
		return $this->promoCodeRepository->findByCode($code);
	}

}
