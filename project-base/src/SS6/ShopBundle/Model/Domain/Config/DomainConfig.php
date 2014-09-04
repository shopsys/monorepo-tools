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
	private $templatesDirectory;

	/**
	 * @param int $id
	 * @param string $domain
	 * @param string $templatesDirectory
	 */
	public function __construct($id, $domain, $templatesDirectory) {
		$this->id = $id;
		$this->domain = $domain;
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
	public function getTemplatesDirectory() {
		return $this->templatesDirectory;
	}

}
