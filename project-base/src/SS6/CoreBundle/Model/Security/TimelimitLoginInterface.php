<?php

namespace SS6\CoreBundle\Model\Security;

interface TimelimitLoginInterface {
	
	/**
	 * @return DateTime
	 */
	public function getLastActivity();
	
	/**
	 * @param DateTime $lastActivity
	 */
	public function setLastActivity($lastActivity);
}
