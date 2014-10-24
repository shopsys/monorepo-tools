<?php

namespace SS6\ShopBundle\Model\Domain\Config;

class DomainConfig {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $domain;

	/**
	 * @var string
	 */
	private $locale;

	/**
	 * @var string
	 */
	private $templatesDirectory;

	/**
	 * @param int $id
	 * @param string $domain
	 * @param string $locale
	 * @param string $templatesDirectory
	 */
	public function __construct($id, $domain, $locale, $templatesDirectory) {
		$this->id = $id;
		$this->domain = $domain;
		$this->locale = $locale;
		$this->templatesDirectory = $templatesDirectory;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * @return string
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * @return string
	 */
	public function getTemplatesDirectory() {
		return $this->templatesDirectory;
	}

}
