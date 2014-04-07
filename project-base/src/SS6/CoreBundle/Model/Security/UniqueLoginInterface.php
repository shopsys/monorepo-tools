<?php

namespace SS6\CoreBundle\Model\Security;

interface UniqueLoginInterface {
	
	/**
	 * @return string
	 */
	public function getLoginToken();
	
	/**
	 * @param string $loginToken
	 */
	public function setLoginToken($loginToken);
}
