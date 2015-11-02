<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface;

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
	 * @var \SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface
	 */
	private $feedItemIteratorFactory;

	/**
	 * @param string $name
	 * @param string $filenamePrefix
	 * @param string $templateFilepath
	 * @param \SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface $feedItemIteratorFactory
	 */
	public function __construct(
		$name,
		$filenamePrefix,
		$templateFilepath,
		FeedItemIteratorFactoryInterface $feedItemIteratorFactory
	) {
		$this->name = $name;
		$this->filenamePrefix = $filenamePrefix;
		$this->templateFilepath = $templateFilepath;
		$this->feedItemIteratorFactory = $feedItemIteratorFactory;
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
	 * @return \SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface
	 */
	public function getFeedItemIteratorFactory() {
		return $this->feedItemIteratorFactory;
	}

}
