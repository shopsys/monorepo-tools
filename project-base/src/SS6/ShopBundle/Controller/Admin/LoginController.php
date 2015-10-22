<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Form\Admin\Login\LoginFormType;
use SS6\ShopBundle\Model\Security\AdministratorLoginFacade;
use SS6\ShopBundle\Model\Security\LoginService;
use SS6\ShopBundle\Model\Security\Roles;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginController extends AdminBaseController {

	const MULTIDOMAIN_LOGIN_TOKEN_PARAMETER_NAME = 'multidomainLoginToken';
	const ORIGINAL_DOMAIN_ID_PARAMETER_NAME = 'originalDomainId';

	/**
	 * @var \SS6\ShopBundle\Model\Security\LoginService
	 */
	private $loginService;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Security\AdministratorLoginFacade
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
	 * @Route("/login_check/", name="admin_login_check")
	 * @Route("/logout/", name="admin_logout")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function loginAction(Request $request) {
		$currentDomainId = $this->domain->getId();
		if ($currentDomainId !== 1 && !$this->isGranted(Roles::ROLE_ADMIN)) {
			$firstDomainRouter = $this->domainRouterFactory->getRouter(1);
			$redirectTo = $firstDomainRouter->generate(
				'admin_login_sso',
				[self::ORIGINAL_DOMAIN_ID_PARAMETER_NAME => $currentDomainId],
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
		} catch (\SS6\ShopBundle\Model\Security\Exception\LoginFailedException $e) {
			$error = 'Přihlášení se nepodařilo.';
		}

		return $this->render('@SS6Shop/Admin/Content/Login/loginForm.html.twig', [
				'form' => $form->createView(),
				'error' => $error,
		]);
	}

	/**
	 * @Route("/sso/{originalDomainId}", requirements={"originalDomainId" = "\d+"})
	 */
	public function ssoAction($originalDomainId) {
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		$this->transactional(function () use ($administrator) {
			$this->administratorLoginFacade->setMultidomainLoginTokenWithExpiration($administrator);
		});
		$originalDomainRouter = $this->domainRouterFactory->getRouter((int)$originalDomainId);
		$redirectTo = $originalDomainRouter->generate(
			'admin_login_authorization',
			[self::MULTIDOMAIN_LOGIN_TOKEN_PARAMETER_NAME => $administrator->getMultidomainLoginToken()],
			UrlGeneratorInterface::ABSOLUTE_URL
		);

		return $this->redirect($redirectTo);
	}

	/**
	 * @Route("/authorization/")
	 */
	public function authorizationAction(Request $request) {
		$multidomainLoginToken = $request->get(self::MULTIDOMAIN_LOGIN_TOKEN_PARAMETER_NAME);
		try {
			$this->administratorLoginFacade->loginByMultidomainToken($request, $multidomainLoginToken);
		} catch (\SS6\ShopBundle\Model\Administrator\Exception\AdministratorException $ex) {
			return $this->render('@SS6Shop/Admin/Content/Login/loginFailed.html.twig');
		}

		return $this->redirectToRoute('admin_default_dashboard');
	}

}
