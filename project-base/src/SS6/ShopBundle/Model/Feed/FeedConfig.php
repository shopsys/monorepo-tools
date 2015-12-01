<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface;

class FeedConfig {

	/**
	 * @var string
	 */
	private $label;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $templateFilepath;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface
	 */
	private $feedItemIteratorFactory;

	/**
	 * @param string $label
	 * @param string $name
	 * @param string $templateFilepath
	 * @param \SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface $feedItemIteratorFactory
	 */
	public function __construct(
		$label,
		$name,
		$templateFilepath,
		FeedItemIteratorFactoryInterface $feedItemIteratorFactory
	) {
		$this->label = $label;
		$this->name = $name;
		$this->templateFilepath = $templateFilepath;
		$this->feedItemIteratorFactory = $feedItemIteratorFactory;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @return string
	 */
	public function getFeedName() {
		return $this->name;
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param string $feedHash
	 *
	 * @return string
	 */
	public function getFeedFilename(DomainConfig $domainConfig, $feedHash) {
		return $feedHash . '_' . $this->name . '_' . $domainConfig->getId() . '.xml';
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
