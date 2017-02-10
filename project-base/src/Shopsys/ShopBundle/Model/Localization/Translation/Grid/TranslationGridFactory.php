<?php

namespace Shopsys\ShopBundle\Model\Localization\Translation\Grid;

use Shopsys\ShopBundle\Component\Grid\ArrayDataSource;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Component\Grid\GridFactoryInterface;
use Shopsys\ShopBundle\Component\Translation\Translator;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Shopsys\ShopBundle\Model\Localization\Translation\TranslationEditFacade;

class TranslationGridFactory implements GridFactoryInterface {

	/**
	 * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\Localization\Translation\TranslationEditFacade
	 */
	private $translationEditFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(
		GridFactory $gridFactory,
		TranslationEditFacade $translationEditFacade,
		Localization $localization
	) {
		$this->gridFactory = $gridFactory;
		$this->translationEditFacade = $translationEditFacade;
		$this->localization = $localization;
	}

	/**
	 * @return \Shopsys\ShopBundle\Component\Grid\Grid
	 */
	public function create() {
		$dataSource = new ArrayDataSource($this->translationEditFacade->getAllTranslationsData(), 'id');

		$grid = $this->gridFactory->create('translationList', $dataSource);

		$grid->addColumn('id', 'id', $this->localization->getLanguageName(Translator::SOURCE_LOCALE));
		foreach ($this->translationEditFacade->getTranslatableLocales() as $locale) {
			$grid->addColumn(
				$locale,
				$locale,
				$this->localization->getLanguageName($locale)
			);
		}

		$grid->setTheme('@ShopsysShop/Admin/Content/Translation/listGrid.html.twig');

		return $grid;
	}

}
