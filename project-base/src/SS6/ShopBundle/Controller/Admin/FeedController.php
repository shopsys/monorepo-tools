<?php

namespace SS6\ShopBundle\Controller\Admin;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Feed\FeedConfigFacade;
use SS6\ShopBundle\Model\Feed\FeedFacade;
use SS6\ShopBundle\Model\Grid\ArrayDataSource;
use SS6\ShopBundle\Model\Grid\GridFactory;

class FeedController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedFacade
	 */
	private $feedFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedConfigFacade
	 */
	private $feedConfigFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	public function __construct(
		FeedFacade $feedFacade,
		FeedConfigFacade $feedConfigFacade,
		GridFactory $gridFactory,
		Domain $domain
	) {
		$this->feedFacade = $feedFacade;
		$this->feedConfigFacade = $feedConfigFacade;
		$this->gridFactory = $gridFactory;
		$this->domain = $domain;
	}

	/**
	 * @Route("/feed/generate/")
	 */
	public function generateAction() {

		$this->feedFacade->generateFeeds();
		$this->feedFacade->generateDeliveryFeeds();
		$this->getFlashMessageSender()->addSuccessFlash('XML Feedy byly vygenerovány');

		return $this->redirectToRoute('admin_feed_list');
	}

	/**
	 * @Route("/feed/list/")
	 */
	public function listAction() {
		$feeds = [];

		$feedConfigs = array_merge(
			$this->feedConfigFacade->getFeedConfigs(),
			$this->feedConfigFacade->getDeliveryFeedConfigs()
		);
		foreach ($feedConfigs as $feedConfig) {
			foreach ($this->domain->getAll() as $domainConfig) {
				$filepath = $this->feedConfigFacade->getFeedFilepath($feedConfig, $domainConfig);
				$feeds[] = [
					'feedName' => $feedConfig->getName(),
					'domainName' => $domainConfig->getName(),
					'url' => $this->feedConfigFacade->getFeedUrl($feedConfig, $domainConfig),
					'created' => file_exists($filepath) ? new DateTime('@' . filemtime($filepath)) : null,
				];
			}
		}

		$dataSource = new ArrayDataSource($feeds, 'name');

		$grid = $this->gridFactory->create('feedsList', $dataSource);

		$grid->addColumn('name', 'feedName', 'Feed');
		$grid->addColumn('created', 'created', 'Vygenerováno');

		$grid->setTheme('@SS6Shop/Admin/Content/Feed/listGrid.html.twig');

		return $this->render('@SS6Shop/Admin/Content/Feed/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

}
