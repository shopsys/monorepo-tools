<?php

namespace SS6\ShopBundle\Model\Feed;

class FeedConfigRepository {

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedDataSourceInterface
	 */
	private $heurekaFeedDataSource;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedDataSourceInterface
	 */
	private $heurekaDeliveryFeedDataSource;

	public function __construct(
		FeedDataSourceInterface $heurekaFeedDataSource,
		FeedDataSourceInterface $heurekaDeliveryFeedDataSource
	) {
		$this->heurekaFeedDataSource = $heurekaFeedDataSource;
		$this->heurekaDeliveryFeedDataSource = $heurekaDeliveryFeedDataSource;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\FeedConfig[]
	 */
	public function getAllFeedConfigs() {
		$feedConfigs = [];

		$feedConfigs[] = new FeedConfig(
			'Heureka',
			'heureka',
			'@SS6Shop/Feed/heureka.xml.twig',
			$this->heurekaFeedDataSource
		);

		return $feedConfigs;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\FeedConfig[]
	 */
	public function getAllDeliveryFeedConfigs() {
		$feedConfigs = [];

		$feedConfigs[] = new FeedConfig(
			'Heureka',
			'heureka_delivery',
			'@SS6Shop/Feed/heurekaDelivery.xml.twig',
			$this->heurekaDeliveryFeedDataSource
		);

		return $feedConfigs;
	}

}
