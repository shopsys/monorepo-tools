<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ShopBundle\Model\Feed\FeedConfig;
use Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface;

class FeedConfigRepository {

	/**
	 * @var \Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface
	 */
	private $heurekaItemRepository;

	/**
	 * @var \Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface
	 */
	private $heurekaDeliveryItemRepository;

	/**
	 * @var \Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface
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
	 * @return \Shopsys\ShopBundle\Model\Feed\FeedConfig[]
	 */
	public function getFeedConfigs() {
		$feedConfigs = [];

		$feedConfigs[] = new FeedConfig(
			'Heureka',
			'heureka',
			'@ShopsysShop/Feed/heureka.xml.twig',
			$this->heurekaItemRepository
		);
		$feedConfigs[] = new FeedConfig(
			'Zboží.cz',
			'zbozi',
			'@ShopsysShop/Feed/zbozi.xml.twig',
			$this->zboziItemRepository
		);

		return $feedConfigs;
	}

	/**
	 * @param string $feedName
	 * @return \Shopsys\ShopBundle\Model\Feed\FeedConfig
	 */
	public function getFeedConfigByName($feedName) {
		foreach ($this->getAllFeedConfigs() as $feedConfig) {
			if ($feedConfig->getFeedName() === $feedName) {
				return $feedConfig;
			}
		}

		$message = 'Feed config with name "' . $feedName . ' not found.';
		throw new \Shopsys\ShopBundle\Model\Feed\Exception\FeedConfigNotFoundException($message);
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Feed\FeedConfig[]
	 */
	public function getDeliveryFeedConfigs() {
		$feedConfigs = [];

		$feedConfigs[] = new FeedConfig(
			t('%feedName% - availability', ['%feedName%' => 'Heureka']),
			'heureka_delivery',
			'@ShopsysShop/Feed/heurekaDelivery.xml.twig',
			$this->heurekaDeliveryItemRepository
		);

		return $feedConfigs;
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Feed\FeedConfig[]
	 */
	public function getAllFeedConfigs() {
		return array_merge($this->getFeedConfigs(), $this->getDeliveryFeedConfigs());
	}

}
