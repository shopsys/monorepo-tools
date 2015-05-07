<?php

namespace SS6\ShopBundle\Form\Admin\Advert;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Advert\Advert;
use SS6\ShopBundle\Model\Advert\AdvertPositionRepository;
use SS6\ShopBundle\Twig\ImageExtension;

class AdvertFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Advert\AdvertPositionRepository  $advertPositionRepository
	 */
	private $advertPositionRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator $translator
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Twig\ImageExtension $imageExtension
	 */
	private $imageExtension;

	public function __construct(
		ImageExtension $imageExtension,
		AdvertPositionRepository $advertPositionRepository,
		Translator $translator
	) {
		$this->imageExtension = $imageExtension;
		$this->advertPositionRepository = $advertPositionRepository;
		$this->translator = $translator;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Advert\Advert $advert
	 * @return \SS6\ShopBundle\Form\Admin\Advert\AdvertFormType
	 */
	public function create(Advert $advert = null) {
		$advertPositionsLocalizedNamesByName = [];
		foreach ($this->advertPositionRepository->getPositionsIndexedByName() as $positionName => $advertPosition) {
			$advertPositionsLocalizedNamesByName[$positionName] = $advertPosition->getLocalizedName();
		}
		$imageExists = false;
		if ($advert !== null) {
			$imageExists = $this->imageExtension->imageExists($advert);
		}

		return new AdvertFormType(
			$imageExists,
			$this->translator,
			$advertPositionsLocalizedNamesByName
		);
	}

}