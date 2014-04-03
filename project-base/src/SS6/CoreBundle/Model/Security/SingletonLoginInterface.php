<?php

namespace SS6\CoreBundle\Model\Security;

interface SingletonLoginInterface {
	
	/**
	 * @return string
	 */
	public function getLoginToken();
	
	/**
	 * @param string $loginToken
	 */
	public function setLoginToken($loginToken);
}
