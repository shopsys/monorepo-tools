<?php

namespace SS6\ShopBundle\Model\Advert;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Advert\AdvertPosition;
use SS6\ShopBundle\Model\Advert\AdvertPositionRepository;

class AdvertPositionRepositoryFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator $translator
	 */
	private $translator;

	/**
	 * @param \SS6\ShopBundle\Component\Translation\Translator $translator
	 */
	public function __construct(Translator $translator) {
		$this->translator = $translator;
	}

	public function create() {
		$advertPositions = [];

		$advertPositions[] = new AdvertPosition(
			AdvertPosition::POSITION_HEADER,
			$this->translator->trans('pod hlavičkou')
		);

		$advertPositions[] = new AdvertPosition(
			AdvertPosition::POSITION_FOOTER,
			$this->translator->trans('nad patičkou')
		);

		$advertPositions[] = new AdvertPosition(
			AdvertPosition::POSITION_PRODUCT_LIST,
			$this->translator->trans('v kategorii (nad názvem kategorie)')
		);

		$advertPositions[] = new AdvertPosition(
			AdvertPosition::POSITION_LEFT_SIDEBAR,
			$this->translator->trans('v levém panelu (pod stromem kategorií)')
		);

		return new AdvertPositionRepository($advertPositions);
	}

}
