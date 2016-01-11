<?php

namespace SS6\ShopBundle\Model\Feed;

class FeedGenerationConfig {

	/**
	 * @var string
	 */
	private $feedName;

	/**
	 * @var int
	 */
	private $domainId;

	/**
	 * @var int|null
	 */
	private $feedItemId;

	/**
	 * @param string $feedName
	 * @param string $domainId
	 * @param int|null $feedItemId
	 */
	public function __construct($feedName, $domainId, $feedItemId = null) {
		$this->feedName = $feedName;
		$this->domainId = $domainId;
		$this->feedItemId = $feedItemId;
	}

	/**
	 * @return string
	 */
	public function getFeedName() {
		return $this->feedName;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @return int|null
	 */
	public function getFeedItemId() {
		return $this->feedItemId;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedGenerationConfig $feedGenerationConfig
	 * @return bool
	 */
	public function isSameFeedAndDomain(FeedGenerationConfig $feedGenerationConfig) {
		return $this->feedName === $feedGenerationConfig->feedName && $this->domainId === $feedGenerationConfig->domainId;
	}

}
