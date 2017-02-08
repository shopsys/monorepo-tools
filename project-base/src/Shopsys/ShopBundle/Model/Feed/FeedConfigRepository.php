<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Model\Feed\FeedConfig;
use SS6\ShopBundle\Model\Feed\FeedItemRepositoryInterface;

class FeedConfigRepository {

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedItemRepositoryInterface
	 */
	private $heurekaItemRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedItemRepositoryInterface
	 */
	private $heurekaDeliveryItemRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedItemRepositoryInterface
	 */
	private $zboziItemRepository;

	public function __construct(
		FeedItemRepositoryInterface $heurekaItemRepository,
		FeedItemRepositoryInterface $heurekaDeliveryItemRepository,
		FeedItemRepositoryInterface $zboziItemRepository
	) {
		$this->heurekaItemRepository = $heurekaItemRepository;
		$this->heurekaDeliveryItemRepository = $heurekaDeliveryItemRepository;
		$this->zboziItemRepository = $zboziItemRepository;
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
			$this->heurekaItemRepository
		);
		$feedConfigs[] = new FeedConfig(
			'Zboží.cz',
			'zbozi',
			'@SS6Shop/Feed/zbozi.xml.twig',
			$this->zboziItemRepository
		);

		return $feedConfigs;
	}

	/**
	 * @param string $feedName
	 * @return \SS6\ShopBundle\Model\Feed\FeedConfig
	 */
	public function getFeedConfigByName($feedName) {
		foreach ($this->getAllFeedConfigs() as $feedConfig) {
			if ($feedConfig->getFeedName() === $feedName) {
				return $feedConfig;
			}
		}

		$message = 'Feed config with name "' . $feedName . ' not found.';
		throw new \SS6\ShopBundle\Model\Feed\Exception\FeedConfigNotFoundException($message);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\FeedConfig[]
	 */
	public function getDeliveryFeedConfigs() {
		$feedConfigs = [];

		$feedConfigs[] = new FeedConfig(
			t('%feedName% - availability', ['%feedName%' => 'Heureka']),
			'heureka_delivery',
			'@SS6Shop/Feed/heurekaDelivery.xml.twig',
			$this->heurekaDeliveryItemRepository
		);

		return $feedConfigs;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\FeedConfig[]
	 */
	public function getAllFeedConfigs() {
		return array_merge($this->getFeedConfigs(), $this->getDeliveryFeedConfigs());
	}

}
