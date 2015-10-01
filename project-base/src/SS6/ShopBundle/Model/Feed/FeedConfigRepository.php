<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Component\Translation\Translator;

class FeedConfigRepository {

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedDataSourceInterface
	 */
	private $heurekaFeedDataSource;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedDataSourceInterface
	 */
	private $heurekaDeliveryFeedDataSource;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	public function __construct(
		FeedDataSourceInterface $heurekaFeedDataSource,
		FeedDataSourceInterface $heurekaDeliveryFeedDataSource,
		Translator $translator
	) {
		$this->heurekaFeedDataSource = $heurekaFeedDataSource;
		$this->heurekaDeliveryFeedDataSource = $heurekaDeliveryFeedDataSource;
		$this->translator = $translator;
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
			$this->heurekaFeedDataSource
		);

		return $feedConfigs;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\FeedConfig[]
	 */
	public function getDeliveryFeedConfigs() {
		$feedConfigs = [];

		$feedConfigs[] = new FeedConfig(
			$this->translator->trans('%feedName% - dostupnostní', ['%feedName%' => 'Heureka']),
			'heureka_delivery',
			'@SS6Shop/Feed/heurekaDelivery.xml.twig',
			$this->heurekaDeliveryFeedDataSource
		);

		return $feedConfigs;
	}

}
