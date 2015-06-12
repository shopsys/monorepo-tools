<?php

namespace SS6\ShopBundle\Model\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class LoginService {

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return bool
	 */
	public function checkLoginProcess(Request $request) {
		$error = null;

		if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
		} else {
			$session = $request->getSession();
			$error = $session->get(Security::AUTHENTICATION_ERROR);
			$session->remove(Security::AUTHENTICATION_ERROR);
		}

		if ($error !== null) {
			throw new \SS6\ShopBundle\Model\Security\Exception\LoginFailedException('Login failed.');
		}

		return true;
	}
}
