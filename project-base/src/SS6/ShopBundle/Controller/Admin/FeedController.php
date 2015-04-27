<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\Feed\FeedFacade;
use SS6\ShopBundle\Model\Grid\ArrayDataSource;
use SS6\ShopBundle\Model\Grid\GridFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FeedController extends Controller {

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

		$htmlList = '';
		foreach ($feeds as $name => $url) {
			$htmlList .= '<li><a href="' . $url . '">' . $name . '</li>';
		}

		return new Response('<body>generated<br /><ul>' . $htmlList . '</ul></body>');
	}

	/**
	 * @Route("/feed/")
	 */
	public function listAction() {
		$feeds = [
			[
				'name' => 'Heureka 1',
				'url' => $this->container->getParameter('ss6.feed_url_prefix') . 'heureka_1.xml',
			],
			[
				'name' => 'Heureka 2',
				'url' => $this->container->getParameter('ss6.feed_url_prefix') . 'heureka_2.xml',
			],
		];

		$dataSource = new ArrayDataSource($feeds, 'name');

		$grid = $this->gridFactory->create('feedsList', $dataSource);

		$grid->addColumn('name', 'name', 'Feed');

		$grid->setTheme('@SS6Shop/Admin/Content/Feed/listGrid.html.twig');

		return $this->render('@SS6Shop/Admin/Content/Feed/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

}
