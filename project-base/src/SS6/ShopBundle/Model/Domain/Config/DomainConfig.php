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
	private $url;

	/**
	 * @var string
	 */
	private $name;

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
	 * @param string $url
	 * @param string $name
	 * @param string $locale
	 * @param string $templatesDirectory
	 */
	public function __construct($id, $url, $name, $locale, $templatesDirectory) {
		$this->id = $id;
		$this->url = $url;
		$this->name = $name;
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
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
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
