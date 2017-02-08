<?php

namespace Shopsys\ShopBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig_SimpleFunction;

class FormThemeExtension extends \Twig_Extension {

	const ADMIN_THEME = '@SS6Shop/Admin/Form/theme.html.twig';
	const FRONT_THEME = '@SS6Shop/Front/Form/theme.html.twig';

	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	protected $request;

	/**
	 * @var \Symfony\Component\HttpFoundation\RequestStack
	 */
	protected $requestStack;

	/**
	 * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
	 */
	public function __construct(RequestStack $requestStack) {
		$this->requestStack = $requestStack;
		$this->request = $this->requestStack->getMasterRequest();
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('getDefaultFormTheme', [$this, 'getDefaultFormTheme']),
		];
	}

	/**
	 * @return string
	 */
	public function getDefaultFormTheme() {
		if (mb_stripos($this->request->get('_controller'), 'Shopsys\ShopBundle\Controller\Admin') === 0) {
			return self::ADMIN_THEME;
		} else {
			return self::FRONT_THEME;
		}
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'form_theme';
	}

}
