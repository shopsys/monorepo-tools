<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedDataSourceInterface;

class FeedConfig {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $filenamePrefix;

	/**
	 * @var string
	 */
	private $templateFilepath;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedDataSourceInterface
	 */
	private $feedDataSource;

	/**
	 * @param string $name
	 * @param string $filenamePrefix
	 * @param string $templateFilepath
	 * @param \SS6\ShopBundle\Model\Feed\FeedDataSourceInterface $feedDataSource
	 */
	public function __construct(
		$name,
		$filenamePrefix,
		$templateFilepath,
		FeedDataSourceInterface $feedDataSource
	) {
		$this->name = $name;
		$this->filenamePrefix = $filenamePrefix;
		$this->templateFilepath = $templateFilepath;
		$this->feedDataSource = $feedDataSource;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return string
	 */
	public function getFeedFilename(DomainConfig $domainConfig) {
		return $this->filenamePrefix . '_' . $domainConfig->getId() . '.xml';
	}

	/**
	 * @return string
	 */
	public function getTemplateFilepath() {
		return $this->templateFilepath;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\FeedDataSourceInterface
	 */
	public function getFeedDataSource() {
		return $this->feedDataSource;
	}

}
