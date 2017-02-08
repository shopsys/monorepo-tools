<?php

namespace Shopsys\ShopBundle\Form\Admin\PromoCode;

use Shopsys\ShopBundle\Form\Admin\PromoCode\PromoCodeFormType;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode;
use Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeRepository;

class PromoCodeFormTypeFactory {

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCodeRepository
	 */
	private $promoCodeRepository;

	public function __construct(PromoCodeRepository $promoCodeRepository) {
		$this->promoCodeRepository = $promoCodeRepository;
	}

	/**
	 * @return \Shopsys\ShopBundle\Form\Admin\PromoCode\PromoCodeFormType
	 */
	public function create() {
		return new PromoCodeFormType($this->getAllPromoCodes());
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode $promoCode
	 * @return \Shopsys\ShopBundle\Form\Admin\PromoCode\PromoCodeFormType
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
	 * @param \Shopsys\ShopBundle\Model\Order\PromoCode\PromoCode $editedPromoCode
	 * @return string[]
	 */
	private function getAllPromoCodesExceptEdited(PromoCode $editedPromoCode) {
		$existingPromoCodes = $this->getAllPromoCodes();
		unset($existingPromoCodes[$editedPromoCode->getId()]);

		return $existingPromoCodes;
	}
}
