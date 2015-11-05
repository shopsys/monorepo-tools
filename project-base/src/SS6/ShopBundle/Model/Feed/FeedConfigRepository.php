<?php

namespace SS6\ShopBundle\Model\Feed;

class FeedConfigRepository {

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface
	 */
	private $heurekaItemIteratorFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface
	 */
	private $heurekaDeliveryItemIteratorFactory;

	public function __construct(
		FeedItemIteratorFactoryInterface $heurekaItemIteratorFactory,
		FeedItemIteratorFactoryInterface $heurekaDeliveryItemIteratorFactory
	) {
		$this->heurekaItemIteratorFactory = $heurekaItemIteratorFactory;
		$this->heurekaDeliveryItemIteratorFactory = $heurekaDeliveryItemIteratorFactory;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\FeedConfig[]
	 */
	public function getFeedConfigs() {
		$feedConfigs = [];

		$feedConfigs[] = new FeedConfig(
			'Heureka',
			'heureka',
			'@SS6Shop/Feed/heureka.xml.twig',
			$this->heurekaItemIteratorFactory
		);

		return $feedConfigs;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\FeedConfig[]
	 */
	public function getDeliveryFeedConfigs() {
		$feedConfigs = [];

		$feedConfigs[] = new FeedConfig(
			t('%feedName% - dostupnostní', ['%feedName%' => 'Heureka']),
			'heureka_delivery',
			'@SS6Shop/Feed/heurekaDelivery.xml.twig',
			$this->heurekaDeliveryItemIteratorFactory
		);

		return $feedConfigs;
	}

}
