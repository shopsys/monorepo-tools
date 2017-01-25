<?php

namespace SS6\ShopBundle\Component\Domain\Config;

class DomainConfig {

	const STYLES_DIRECTORY_DEFAULT = 'common';

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
	private $stylesDirectory;

	/**
	 * @param int $id
	 * @param string $url
	 * @param string $name
	 * @param string $locale
	 * @param $stylesDirectory
	 */
	public function __construct($id, $url, $name, $locale, $stylesDirectory) {
		$this->id = $id;
		$this->url = $url;
		$this->name = $name;
		$this->locale = $locale;
		$this->stylesDirectory = $stylesDirectory;
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
	public function getStylesDirectory() {
		return $this->stylesDirectory;
	}

	/**
	 * @return bool
	 */
	public function isHttps() {
		return strpos($this->url, 'https://') === 0;
	}

}
