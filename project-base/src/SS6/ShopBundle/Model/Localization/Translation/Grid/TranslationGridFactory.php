<?php

namespace SS6\ShopBundle\Model\Localization\Translation\Grid;

use SS6\ShopBundle\Component\Translator;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\GridFactoryInterface;
use SS6\ShopBundle\Model\Grid\ArrayDataSource;

class TranslationGridFactory implements GridFactoryInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Translator
	 */
	private $translator;

	public function __construct(
		GridFactory $gridFactory,
		Translator $translator
	) {
		$this->gridFactory = $gridFactory;
		$this->translator = $translator;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create() {
		$dataSource = new ArrayDataSource($this->loadData(), 'id');

		$grid = $this->gridFactory->create('translationList', $dataSource);

		$grid->addColumn('id', 'id', 'Konstanta');
		$grid->addColumn('cs', 'cs', 'Česky');
		$grid->addColumn('en', 'en', 'Anglicky');

		return $grid;
	}

	/**
	 * @return array
	 */
	private function loadData() {
		$catalogueCs = $this->translator->getCalatogue('cs');
		$catalogueEn = $this->translator->getCalatogue('en');

		$data = array();
		foreach ($catalogueCs->all(Translator::DEFAULT_DOMAIN) as $id => $translation) {
			$data[$id]['id'] = $id;
			$data[$id]['cs'] = $translation;
			$data[$id]['en'] = null;
		}

		foreach ($catalogueEn->all(Translator::DEFAULT_DOMAIN) as $id => $translation) {
			$data[$id]['id'] = $id;
			if (!isset($data[$id]['cs'])) {
				$data[$id]['cs'] = null;
			}
			$data[$id]['en'] = $translation;
		}

		return $data;
	}

}
