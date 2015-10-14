<?php

namespace SS6\ShopBundle\Form\Admin\TermsAndConditions;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Article\ArticleEditFacade;

class TermsAndConditionsSettingFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleEditFacade
	 */
	private $articleEditFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(ArticleEditFacade $articleEditFacade, Translator $translator) {
		$this->articleEditFacade = $articleEditFacade;
		$this->translator = $translator;
	}

	public function createForDomain($domainId) {
		$articles = $this->articleEditFacade->getAllByDomainId($domainId);

		return new TermsAndConditionsSettingFormType($articles, $this->translator);
	}
}
