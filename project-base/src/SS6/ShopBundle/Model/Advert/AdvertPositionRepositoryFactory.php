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
			'header',
			$this->translator->trans('pod Hlavičkou')
		);

		$advertPositions[] = new AdvertPosition(
			'footer',
			$this->translator->trans('nad Patičkou')
		);

		$advertPositions[] = new AdvertPosition(
			'product_list',
			$this->translator->trans('Seznam produktu')
		);

		$advertPositions[] = new AdvertPosition(
			'left_sidebar',
			$this->translator->trans('Levé menu')
		);

		return new AdvertPositionRepository($advertPositions);
	}

}
