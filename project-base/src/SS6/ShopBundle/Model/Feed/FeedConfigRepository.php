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

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedItemIteratorFactoryInterface
	 */
	private $zboziItemIteratorFactory;

	public function __construct(
		FeedItemIteratorFactoryInterface $heurekaItemIteratorFactory,
		FeedItemIteratorFactoryInterface $heurekaDeliveryItemIteratorFactory,
		FeedItemIteratorFactoryInterface $zboziItemIteratorFactory
	) {
		$this->heurekaItemIteratorFactory = $heurekaItemIteratorFactory;
		$this->heurekaDeliveryItemIteratorFactory = $heurekaDeliveryItemIteratorFactory;
		$this->zboziItemIteratorFactory = $zboziItemIteratorFactory;
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
		$feedConfigs[] = new FeedConfig(
			'Zboží.cz',
			'zbozi',
			'@SS6Shop/Feed/zbozi.xml.twig',
			$this->zboziItemIteratorFactory
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
