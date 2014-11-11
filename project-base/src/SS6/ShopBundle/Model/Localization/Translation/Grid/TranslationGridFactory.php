<?php

namespace SS6\ShopBundle\Model\Localization\Translation\Grid;

use SS6\ShopBundle\Component\Translator;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\ArrayDataSource;
use SS6\ShopBundle\Model\Localization\Translation\TranslationEditFacade;

class TranslationGridFactory implements GridFactoryInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Translator
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Translation\TranslationEditFacade
	 */
	private $translationEditFacade;

	public function __construct(
		GridFactory $gridFactory,
		Translator $translator,
		TranslationEditFacade $translationEditFacade
	) {
		$this->gridFactory = $gridFactory;
		$this->translator = $translator;
		$this->translationEditFacade = $translationEditFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create() {
		$dataSource = new ArrayDataSource($this->translationEditFacade->getAllTranslations(), 'id');

		$grid = $this->gridFactory->create('translationList', $dataSource);

		$grid->addColumn('id', 'id', 'Konstanta');
		$grid->addColumn('cs', 'cs', 'Česky');
		$grid->addColumn('en', 'en', 'Anglicky');

		return $grid;
	}

}
