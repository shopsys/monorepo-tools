<?php

namespace SS6\ShopBundle\Model\Security;

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
