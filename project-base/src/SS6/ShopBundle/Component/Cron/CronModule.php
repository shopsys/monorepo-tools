<?php

namespace SS6\ShopBundle\Component\Cron;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cron_modules")
 * @ORM\Entity
 */
class CronModule {

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=255)
	 * @ORM\Id
	 */
	private $moduleId;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $scheduled;

	/**
	 * @param string $moduleId
	 */
	public function __construct($moduleId) {
		$this->moduleId = $moduleId;
		$this->scheduled = false;
	}

	/**
	 * @return string
	 */
	public function getModuleId() {
		return $this->moduleId;
	}

	/**
	 * @return bool
	 */
	public function isScheduled() {
		return $this->scheduled;
	}

	public function schedule() {
		$this->scheduled = true;
	}

	public function unschedule() {
		$this->scheduled = false;
	}

}
