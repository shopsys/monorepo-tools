<?php

namespace SS6\CoreBundle\Model\Security\Service;

use SS6\CoreBundle\Model\Security\Exception\LoginFailedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class LoginServiceTest {

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return bool
	 */
	public function checkLoginProcess(Request $request) {
		$error = null;
		
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} else {
			$session = $request->getSession();
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}

		if ($error !== null) {
			throw new LoginFailedException('Login failed.');
		}
		
		return true;
	}
}