<?php

namespace SS6\ShopBundle\Component\Form;

use DateInterval;
use DateTime;
use SS6\ShopBundle\Form\TimedFormTypeExtension;
use Symfony\Component\HttpFoundation\Session\Session;

class FormTimeProvider {

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $session;

	/**
	 * @param \Symfony\Component\HttpFoundation\Session\Session $session
	 */
	public function __construct(Session $session) {
		$this->session = $session;
	}

	/**
	 * @param string $name
	 * @return DateTime
	 */
	public function generateFormTime($name) {
		$startTime = new DateTime();
		$key = $this->getSessionKey($name);
		$this->session->set($key, $startTime);
		return $startTime;
	}

	/**
	 * @param string $name
	 * @param array $options
	 * @return bool
	 */
	public function isFormTimeValid($name, array $options) {
		$isValid = true;
		$startTime = $this->getFormTime($name);

		if ($startTime === false) {
			return false;
		}

		if ($options[TimedFormTypeExtension::OPTION_MINIMUM_SECONDS] !== null) {
			$currentTime = new DateTime();
			$validTime = $startTime->add(
				DateInterval::createFromDateString($options[TimedFormTypeExtension::OPTION_MINIMUM_SECONDS] . 'seconds')
			);
			$isValid = $validTime < $currentTime;
		}

		return $isValid;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasFormTime($name) {
		$key = $this->getSessionKey($name);
		return $this->session->has($key);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function getFormTime($name) {
		$key = $this->getSessionKey($name);
		if ($this->hasFormTime($name)) {
			return $this->session->get($key);
		}
		return false;
	}

	/**
	 * @param string $name
	 */
	public function removeFormTime($name) {
		$key = $this->getSessionKey($name);
		$this->session->remove($key);
	}

	/**
	 * @param string $name
	 * @return string
	 */
	protected function getSessionKey($name) {
		return 'timedSpam-' . $name;
	}

}
