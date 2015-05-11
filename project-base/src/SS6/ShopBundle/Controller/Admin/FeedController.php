<?php

namespace SS6\ShopBundle\Controller\Admin;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Model\Domain\Domain;
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

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(FeedFacade $feedFacade, GridFactory $gridFactory, Domain $domain) {
		$this->feedFacade = $feedFacade;
		$this->gridFactory = $gridFactory;
		$this->domain = $domain;
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
		$feeds = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$filename = 'heureka_' . $domainConfig->getId() . '.xml';
			$heurekaFilepath = $this->container->getParameter('ss6.feed_dir') . '/' . $filename;
			$feeds[] = [
				'name' => $domainConfig->getName() . ' - Heureka',
				'url' => $domainConfig->getUrl() . $this->container->getParameter('ss6.feed_url_prefix') . $filename,
				'created' => file_exists($heurekaFilepath) ? new DateTime('@' . filemtime($heurekaFilepath)) : null,
			];
		}

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
