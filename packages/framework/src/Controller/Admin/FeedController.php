<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Grid\ArrayDataSource;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Model\Feed\FeedFacade;
use Shopsys\FrameworkBundle\Model\Security\Roles;

class FeedController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedFacade
     */
    private $feedFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    public function __construct(
        FeedFacade $feedFacade,
        GridFactory $gridFactory,
        Domain $domain
    ) {
        $this->feedFacade = $feedFacade;
        $this->gridFactory = $gridFactory;
        $this->domain = $domain;
    }

    /**
     * @Route("/feed/generate/{feedName}/{domainId}", requirements={"domainId" = "\d+"})
     * @param string $feedName
     * @param int $domainId
     */
    public function generateAction($feedName, $domainId)
    {
        $domainConfig = $this->domain->getDomainConfigById((int)$domainId);

        try {
            $this->feedFacade->generateFeed($feedName, $domainConfig);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Feed "{{ feedName }}" successfully generated.'),
                [
                    'feedName' => $feedName,
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Feed\Exception\FeedNotFoundException $ex) {
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
    public function listAction()
    {
        $feedsData = [];

        $feedsInfo = $this->feedFacade->getFeedsInfo();
        foreach ($feedsInfo as $feedInfo) {
            foreach ($this->domain->getAll() as $domainConfig) {
                $feedTimestamp = $this->feedFacade->getFeedTimestamp($feedInfo, $domainConfig);
                $feedsData[] = [
                    'feedLabel' => $feedInfo->getLabel(),
                    'feedName' => $feedInfo->getName(),
                    'domainConfig' => $domainConfig,
                    'url' => $this->feedFacade->getFeedUrl($feedInfo, $domainConfig),
                    'created' => $feedTimestamp === null ? null : (new DateTime())->setTimestamp($feedTimestamp),
                    'actions' => null,
                    'additionalInformation' => $feedInfo->getAdditionalInformation(),
                ];
            }
        }

        $dataSource = new ArrayDataSource($feedsData, 'label');

        $grid = $this->gridFactory->create('feedsList', $dataSource);

        $grid->addColumn('label', 'feedLabel', t('Feed'));
        $grid->addColumn('created', 'created', t('Generated'));
        $grid->addColumn('url', 'url', t('Url address'));
        if ($this->isGranted(Roles::ROLE_SUPER_ADMIN)) {
            $grid->addColumn('actions', 'actions', t('Action'))->setClassAttribute('column--superadmin');
        }

        $grid->setTheme('@ShopsysFramework/Admin/Content/Feed/listGrid.html.twig');

        return $this->render('@ShopsysFramework/Admin/Content/Feed/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }
}
