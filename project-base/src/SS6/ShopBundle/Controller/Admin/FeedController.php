<?php

namespace SS6\ShopBundle\Controller\Admin;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Model\Feed\FeedFacade;
use SS6\ShopBundle\Model\Grid\ArrayDataSource;
use SS6\ShopBundle\Model\Grid\GridFactory;

class FeedController extends BaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedFacade
	 */
	private $feedFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	public function __construct(FeedFacade $feedFacade, GridFactory $gridFactory) {
		$this->feedFacade = $feedFacade;
		$this->gridFactory = $gridFactory;
	}

	/**
	 * @Route("/feed/generate/")
	 */
	public function generateAction() {

		$this->feedFacade->generateAllFeeds();
		$this->getFlashMessageSender()->addSuccessFlash('XML Feedy byly vygenerovány');

		return $this->redirectToRoute('admin_feed_list');
	}

	/**
	 * @Route("/feed/list/")
	 */
	public function listAction() {
		$heureka1Filepath = $this->container->getParameter('ss6.feed_dir') . '/heureka_1.xml';
		$heureka2Filepath = $this->container->getParameter('ss6.feed_dir') . '/heureka_2.xml';

		$feeds = [
			[
				'name' => 'Heureka 1',
				'url' => $this->container->getParameter('ss6.feed_url_prefix') . 'heureka_1.xml',
				'created' => file_exists($heureka1Filepath) ? new DateTime('@' . filemtime($heureka1Filepath)) : null,
			],
			[
				'name' => 'Heureka 2',
				'url' => $this->container->getParameter('ss6.feed_url_prefix') . 'heureka_2.xml',
				'created' => file_exists($heureka2Filepath) ? new DateTime('@' . filemtime($heureka2Filepath)) : null,
			],
		];

		$dataSource = new ArrayDataSource($feeds, 'name');

		$grid = $this->gridFactory->create('feedsList', $dataSource);

		$grid->addColumn('name', 'name', 'Feed');
		$grid->addColumn('created', 'created', 'Vygenerováno');

		$grid->setTheme('@SS6Shop/Admin/Content/Feed/listGrid.html.twig');

		return $this->render('@SS6Shop/Admin/Content/Feed/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

}
