<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Grid\ArrayDataSource;
use Shopsys\ShopBundle\Component\Grid\GridFactory;
use Shopsys\ShopBundle\Model\Feed\FeedConfigFacade;
use Shopsys\ShopBundle\Model\Feed\FeedFacade;
use Shopsys\ShopBundle\Model\Security\Roles;

class FeedController extends AdminBaseController {

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Shopsys\ShopBundle\Model\Feed\FeedFacade
	 */
	private $feedFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Feed\FeedConfigFacade
	 */
	private $feedConfigFacade;

	/**
	 * @var \Shopsys\ShopBundle\Component\Grid\GridFactory
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
	 * @Route("/feed/generate/{feedName}/{domainId}", requirements={"domainId" = "\d+"})
	 * @param string $feedName
	 * @param int $domainId
	 */
	public function generateAction($feedName, $domainId) {
		try {
			$feedConfig = $this->feedConfigFacade->getFeedConfigByName($feedName);
			$domainConfig = $this->domain->getDomainConfigById((int)$domainId);

			$this->feedFacade->generateFeed($feedConfig, $domainConfig);
			$this->getFlashMessageSender()->addSuccessFlashTwig(
				t('Feed "{{ feedName }}" successfully generated.'),
				[
					'feedName' => $feedName,
				]
			);

		} catch (\Shopsys\ShopBundle\Model\Feed\Exception\FeedConfigNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlashTwig(
				t('Feed "{{ feedName }}" not found.'),
				[
					'feedName' => $feedName,
				]
			);
		}

		return $this->redirectToRoute('admin_feed_list');
	}

	/**
	 * @Route("/feed/list/")
	 */
	public function listAction() {
		$feeds = [];

		$feedConfigs = $this->feedConfigFacade->getAllFeedConfigs();
		foreach ($feedConfigs as $feedConfig) {
			foreach ($this->domain->getAll() as $domainConfig) {
				$filepath = $this->feedConfigFacade->getFeedFilepath($feedConfig, $domainConfig);
				$feeds[] = [
					'feedLabel' => $feedConfig->getLabel(),
					'feedName' => $feedConfig->getFeedName(),
					'domainConfig' => $domainConfig,
					'url' => $this->feedConfigFacade->getFeedUrl($feedConfig, $domainConfig),
					'created' => file_exists($filepath) ? new DateTime('@' . filemtime($filepath)) : null,
					'actions' => null,
				];
			}
		}

		$dataSource = new ArrayDataSource($feeds, 'label');

		$grid = $this->gridFactory->create('feedsList', $dataSource);

		$grid->addColumn('label', 'feedLabel', t('Feed'));
		$grid->addColumn('created', 'created', t('Generated'));
		$grid->addColumn('url', 'url', t('Url address'));
		if ($this->isGranted(Roles::ROLE_SUPER_ADMIN)) {
			$grid->addColumn('actions', 'actions', t('Action'))->setClassAttribute('column--superadmin');
		}

		$grid->setTheme('@ShopsysShop/Admin/Content/Feed/listGrid.html.twig');

		return $this->render('@ShopsysShop/Admin/Content/Feed/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

}
