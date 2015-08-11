<?php

namespace SS6\ShopBundle\Form\Admin\PromoCode;

use SS6\ShopBundle\Form\Admin\PromoCode\PromoCodeFormType;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCode;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeRepository;

class PromoCodeFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeRepository
	 */
	private $promoCodeRepository;

	public function __construct(PromoCodeRepository $promoCodeRepository) {
		$this->promoCodeRepository = $promoCodeRepository;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\PromoCode\PromoCodeFormType
	 */
	public function create() {
		return new PromoCodeFormType($this->getAllPromoCodes());
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\PromoCode\PromoCode $promoCode
	 * @return \SS6\ShopBundle\Form\Admin\PromoCode\PromoCodeFormType
	 */
	public function createForPromoCode(PromoCode $promoCode) {
		return new PromoCodeFormType($this->getAllPromoCodesExceptEdited($promoCode));
	}

	/**
	 * @return string[]
	 */
	private function getAllPromoCodes() {
		$allPromoCodes = [];
		foreach ($this->promoCodeRepository->getAll() as $promoCode) {
			$allPromoCodes[$promoCode->getId()] = $promoCode->getCode();
		}

		return $allPromoCodes;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\PromoCode\PromoCode $editedPromoCode
	 * @return string[]
	 */
	private function getAllPromoCodesExceptEdited(PromoCode $editedPromoCode) {
		$existingPromoCodes = $this->getAllPromoCodes();
		unset($existingPromoCodes[$editedPromoCode->getId()]);

		return $existingPromoCodes;
	}
}
