<?php

namespace SS6\ShopBundle\Form\Admin\Advert;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Advert\Advert;
use SS6\ShopBundle\Model\Advert\AdvertPositionList;
use SS6\ShopBundle\Twig\ImageExtension;

class AdvertFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Advert\AdvertPositionList
	 */
	private $advertPositionList;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Twig\ImageExtension
	 */
	private $imageExtension;

	public function __construct(
		ImageExtension $imageExtension,
		AdvertPositionList $advertPositionList,
		Translator $translator
	) {
		$this->imageExtension = $imageExtension;
		$this->advertPositionList = $advertPositionList;
		$this->translator = $translator;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Advert\Advert $advert
	 * @return \SS6\ShopBundle\Form\Admin\Advert\AdvertFormType
	 */
	public function create(Advert $advert = null) {
		$imageExists = false;
		if ($advert !== null) {
			$imageExists = $this->imageExtension->imageExists($advert);
		}

		return new AdvertFormType(
			$imageExists,
			$this->translator,
			$this->advertPositionList->getTranslationsIndexedByValue()
		);
	}

}
