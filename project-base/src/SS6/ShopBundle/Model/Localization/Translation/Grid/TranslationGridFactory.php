<?php

namespace SS6\ShopBundle\Model\Localization\Translation\Grid;

use SS6\ShopBundle\Model\Grid\ArrayDataSource;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Localization\Translation\TranslationEditFacade;
use Symfony\Component\Translation\TranslatorInterface;

class TranslationGridFactory implements GridFactoryInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Translation\TranslationEditFacade
	 */
	private $translationEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(
		GridFactory $gridFactory,
		TranslatorInterface $translator,
		TranslationEditFacade $translationEditFacade,
		Localization $localization
	) {
		$this->gridFactory = $gridFactory;
		$this->translator = $translator;
		$this->translationEditFacade = $translationEditFacade;
		$this->localization = $localization;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create() {
		$dataSource = new ArrayDataSource($this->translationEditFacade->getAllTranslations(), 'id');

		$grid = $this->gridFactory->create('translationList', $dataSource);

		$grid->addColumn('id', 'id', $this->translator->trans('Konstanta'));
		foreach ($this->localization->getAllLocales() as $locale) {
			$grid->addColumn(
				$locale,
				$locale,
				$this->localization->getLanguageName($locale)
			);
		}

		$grid->setTheme('@SS6Shop/Admin/Content/Translation/listGrid.html.twig');

		return $grid;
	}

}
