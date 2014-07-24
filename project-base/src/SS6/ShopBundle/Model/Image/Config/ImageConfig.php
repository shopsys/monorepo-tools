<?php

namespace SS6\ShopBundle\Model\Image\Config;

class ImageConfig {

	const WITHOUT_NAME_KEY = '__NULL__';

	/**
	 * @var array
	 */
	private $configuration;

	public function __construct(array $configuration) {
		$this->configuration = $configuration;
	}

	public function getConfiguration() {
		return $this->configuration;
	}



}
