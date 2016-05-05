<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Model\Cookies\CookiesFacade;
use Twig_SimpleFunction;

class CookiesExtension extends \Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Model\Cookies\CookiesFacade
	 */
	private $cookiesFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Cookies\CookiesFacade $cookiesFacade
	 */
	public function __construct(CookiesFacade $cookiesFacade) {
		$this->cookiesFacade = $cookiesFacade;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('isCookiesConsentGiven', [$this, 'isCookiesConsentGiven']),
			new Twig_SimpleFunction('findCookiesArticleByDomainId', [$this, 'findCookiesArticleByDomainId']),
		];
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'cookies';
	}

	/**
	 * @return bool
	 */
	public function isCookiesConsentGiven() {
		return $this->cookiesFacade->isCookiesConsentGiven();
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Article\Article|null
	 */
	public function findCookiesArticleByDomainId($domainId) {
		return $this->cookiesFacade->findCookiesArticleByDomainId($domainId);
	}
}
