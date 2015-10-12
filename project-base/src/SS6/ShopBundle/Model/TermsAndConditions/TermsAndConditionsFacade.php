<?php

namespace SS6\ShopBundle\Model\TermsAndConditions;

use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Article\ArticleEditFacade;

class TermsAndConditionsFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleEditFacade
	 */
	private $articleEditFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(
		ArticleEditFacade $articleEditFacade,
		Setting $setting,
		Translator $translator
	) {
		$this->articleEditFacade = $articleEditFacade;
		$this->setting = $setting;
		$this->translator = $translator;
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Article\Article|null
	 */
	public function findTermsAndConditionsArticleByDomainId($domainId) {
		$termsAndConditionsArticleId = $this->setting->get(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $domainId);

		if ($termsAndConditionsArticleId !== null) {
			return $this->articleEditFacade->findById(
				$this->setting->get(Setting::TERMS_AND_CONDITIONS_ARTICLE_ID, $domainId)
			);
		}

		return null;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Article\Article|null $termsAndConditionsArticle
	 * @param int $domainId
	 */
	public function setTermsTermsAndConditionsArticleOnDomain($termsAndConditionsArticle, $domainId) {
		$termsAndConditionsArticleId = null;
		if ($termsAndConditionsArticle !== null) {
			$termsAndConditionsArticleId = $termsAndConditionsArticle->getId();
		}
		$this->setting->set(
			Setting::TERMS_AND_CONDITIONS_ARTICLE_ID,
			$termsAndConditionsArticleId,
			$domainId
		);
	}

	/**
	 * @return string
	 */
	public function getDownloadFilename() {
		return $this->translator->trans('Obchodní-podmínky.html');
	}

}
