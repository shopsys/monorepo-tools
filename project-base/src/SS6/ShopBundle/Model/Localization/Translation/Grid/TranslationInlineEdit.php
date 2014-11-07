<?php

namespace SS6\ShopBundle\Model\Localization\Translation\Grid;

use SS6\ShopBundle\Form\Admin\Localization\TranslationFormType;
use SS6\ShopBundle\Model\Grid\InlineEdit\AbstractGridInlineEdit;
use SS6\ShopBundle\Model\Localization\Translation\Grid\TranslationGridFactory;
use SS6\ShopBundle\Component\Translator;
use Symfony\Component\Form\FormFactory;

class TranslationInlineEdit extends AbstractGridInlineEdit {

	/**
	 * @var \SS6\ShopBundle\Component\Translator
	 */
	private $translator;

	public function __construct(
		FormFactory $formFactory,
		TranslationGridFactory $translationGridFactory,
		Translator $translator
	) {
		$this->translator = $translator;

		parent::__construct($formFactory, $translationGridFactory);
	}

	/**
	 * @param array $translationData
	 */
	protected function createEntityAndGetId($translationData) {
		$message = 'Method "createEntityAndGetId" is not supported in translations.';
		throw new \SS6\ShopBundle\Model\Localization\Translation\Grid\Exception\NotImplementedException($message);
	}

	/**
	 * @param int $translationId
	 * @param array $translationData
	 */
	protected function editEntity($translationId, $translationData) {
		// TODO: implementation
	}

	/**
	 * @param int $translationId
	 * @return array
	 */
	protected function getFormDataObject($translationId = null) {
		if ($translationId === null) {
			$message = 'Method "getFormDataObject" for new translation is not supported in translations.';
			throw new \SS6\ShopBundle\Model\Localization\Grid\Exception\NotImplementedException($message);
		}

		$translationData = array(
			'cs' => $this->translator->getCalatogue('cs')->get($translationId),
			'en' => $this->translator->getCalatogue('en')->get($translationId),
		);
		
		return $translationData;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Localization\TranslationFormType
	 */
	protected function getFormType() {
		return new TranslationFormType();
	}

	/**
	 * @return string
	 */
	public function getServiceName() {
		return 'ss6.shop.localization.translation.grid.translation_inline_edit';
	}

}
