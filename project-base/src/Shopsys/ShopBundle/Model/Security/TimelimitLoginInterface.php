<?php

namespace SS6\ShopBundle\Model\Security;

interface TimelimitLoginInterface {

	/**
	 * @return \DateTime
	 */
	public function getLastActivity();

	/**
	 * @param \DateTime $lastActivity
	 */
	public function setLastActivity($lastActivity);
}
