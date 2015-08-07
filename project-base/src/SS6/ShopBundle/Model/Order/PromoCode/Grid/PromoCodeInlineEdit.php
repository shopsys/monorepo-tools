<?php

namespace SS6\ShopBundle\Model\Order\PromoCode\Grid;

use SS6\ShopBundle\Form\Admin\PromoCode\PromoCodeFormType;
use SS6\ShopBundle\Model\Grid\InlineEdit\AbstractGridInlineEdit;
use SS6\ShopBundle\Model\Order\PromoCode\Grid\PromoCodeGridFactory;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeData;
use SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade;
use Symfony\Component\Form\FormFactory;

class PromoCodeInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade
	 */
	private $promoCodeFacade;

	/**
	 * @param \Symfony\Component\Form\FormFactory $formFactory
	 * @param \SS6\ShopBundle\Model\Order\PromoCode\Grid\PromoCodeGridFactory $promoCodeGridFactory
	 * @param \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeFacade $promoCodeFacade
	 */
	public function __construct(
		FormFactory $formFactory,
		PromoCodeGridFactory $promoCodeGridFactory,
		PromoCodeFacade $promoCodeFacade
	) {
		$this->promoCodeFacade = $promoCodeFacade;

		parent::__construct($formFactory, $promoCodeGridFactory);
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return self::class;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
	 * @return int
	 */
	protected function createEntityAndGetId($promoCodeData) {
		$promoCode = $this->promoCodeFacade->create($promoCodeData);

		return $promoCode->getId();
	}

	/**
	 * @param int $promoCodeId
	 * @param \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeData $promoCodeData
	 */
	protected function editEntity($promoCodeId, $promoCodeData) {
		$this->promoCodeFacade->edit($promoCodeId, $promoCodeData);
	}

	/**
	 * @param int|null $promoCodeId
	 * @return \SS6\ShopBundle\Model\Order\PromoCode\PromoCodeData
	 */
	protected function getFormDataObject($promoCodeId = null) {
		$promoCodeData = new PromoCodeData();

		if ($promoCodeId !== null) {
			$promoCodeId = (int)$promoCodeId;
			$promoCode = $this->promoCodeFacade->getById($promoCodeId);
			$promoCodeData->setFromEntity($promoCode);
		}

		return $promoCodeData;
	}

	/**
	 * @param int $rowId
	 * @return \SS6\ShopBundle\Form\Admin\PromoCode\PromoCodeFormType
	 */
	protected function getFormType($rowId) {
		return new PromoCodeFormType($this->promoCodeFacade->getAll());
	}

}
