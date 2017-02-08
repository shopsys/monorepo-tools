<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Shopsys\ShopBundle\Form\Admin\Login\LoginFormType;
use Shopsys\ShopBundle\Model\Security\AdministratorLoginFacade;
use Shopsys\ShopBundle\Model\Security\LoginService;
use Shopsys\ShopBundle\Model\Security\Roles;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginController extends AdminBaseController {

	const MULTIDOMAIN_LOGIN_TOKEN_PARAMETER_NAME = 'multidomainLoginToken';
	const ORIGINAL_DOMAIN_ID_PARAMETER_NAME = 'originalDomainId';
	const ORIGINAL_REFERER_PARAMETER_NAME = 'originalReferer';

	/**
	 * @var \Shopsys\ShopBundle\Model\Security\LoginService
	 */
	private $loginService;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Shopsys\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\Security\AdministratorLoginFacade
	 */
	private $administratorLoginFacade;

	public function __construct(
		LoginService $loginService,
		Domain $domain,
		DomainRouterFactory $domainRouterFactory,
		AdministratorLoginFacade $administratorLoginFacade
	) {
		$this->loginService = $loginService;
		$this->domain = $domain;
		$this->domainRouterFactory = $domainRouterFactory;
		$this->administratorLoginFacade = $administratorLoginFacade;
	}

	/**
	 * @Route("/", name="admin_login")
	 * @Route("/login-check/", name="admin_login_check")
	 * @Route("/logout/", name="admin_logout")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function loginAction(Request $request) {
		$currentDomainId = $this->domain->getId();
		if ($currentDomainId !== Domain::MAIN_ADMIN_DOMAIN_ID && !$this->isGranted(Roles::ROLE_ADMIN)) {
			$mainAdminDomainRouter = $this->domainRouterFactory->getRouter(Domain::MAIN_ADMIN_DOMAIN_ID);
			$redirectTo = $mainAdminDomainRouter->generate(
				'admin_login_sso',
				[
					self::ORIGINAL_DOMAIN_ID_PARAMETER_NAME => $currentDomainId,
					self::ORIGINAL_REFERER_PARAMETER_NAME => $request->server->get('HTTP_REFERER'),
				],
				UrlGeneratorInterface::ABSOLUTE_URL
			);

			return $this->redirect($redirectTo);
		}
		if ($this->isGranted(Roles::ROLE_ADMIN)) {
			return $this->redirectToRoute('admin_default_dashboard');
		}

		$error = null;

		$form = $this->createForm(new LoginFormType(), null, [
			'action' => $this->generateUrl('admin_login_check'),
			'method' => 'POST',
		]);

		try {
			$this->loginService->checkLoginProcess($request);
		} catch (\Shopsys\ShopBundle\Model\Security\Exception\LoginFailedException $e) {
			$error = t('Log in failed.');
		}

		return $this->render('@SS6Shop/Admin/Content/Login/loginForm.html.twig', [
				'form' => $form->createView(),
				'error' => $error,
		]);
	}

	/**
	 * @Route("/sso/{originalDomainId}", requirements={"originalDomainId" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $originalDomainId
	 */
	public function ssoAction(Request $request, $originalDomainId) {
		$administrator = $this->getUser();
		/* @var $administrator \Shopsys\ShopBundle\Model\Administrator\Administrator */
		$multidomainToken = $this->administratorLoginFacade->generateMultidomainLoginTokenWithExpiration($administrator);
		$originalDomainRouter = $this->domainRouterFactory->getRouter((int)$originalDomainId);
		$redirectTo = $originalDomainRouter->generate(
			'admin_login_authorization',
			[
				self::MULTIDOMAIN_LOGIN_TOKEN_PARAMETER_NAME => $multidomainToken,
				self::ORIGINAL_REFERER_PARAMETER_NAME => $request->get(self::ORIGINAL_REFERER_PARAMETER_NAME),
			],
			UrlGeneratorInterface::ABSOLUTE_URL
		);

		return $this->redirect($redirectTo);
	}

	/**
	 * @Route("/authorization/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function authorizationAction(Request $request) {
		$multidomainLoginToken = $request->get(self::MULTIDOMAIN_LOGIN_TOKEN_PARAMETER_NAME);
		$originalReferer = $request->get(self::ORIGINAL_REFERER_PARAMETER_NAME);
		try {
			$this->administratorLoginFacade->loginByMultidomainToken($request, $multidomainLoginToken);
		} catch (\Shopsys\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenException $ex) {
			return $this->render('@SS6Shop/Admin/Content/Login/loginFailed.html.twig');
		}
		$redirectTo = ($originalReferer !== null) ? $originalReferer : $this->generateUrl('admin_default_dashboard');

		return $this->redirect($redirectTo);
	}

}
