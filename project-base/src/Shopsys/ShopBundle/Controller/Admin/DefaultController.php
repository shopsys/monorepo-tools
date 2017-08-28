<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Model\Customer\CustomerFacade;
use Shopsys\ShopBundle\Model\Statistics\StatisticsFacade;
use Shopsys\ShopBundle\Model\Statistics\StatisticsProcessingFacade;

class DefaultController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Statistics\StatisticsFacade
     */
    private $statisticsFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Statistics\StatisticsProcessingFacade
     */
    private $statisticsProcessingFacade;

    public function __construct(
        StatisticsFacade $statisticsFacade,
        StatisticsProcessingFacade $statisticsProcessingFacade
    ) {
        $this->statisticsFacade = $statisticsFacade;
        $this->statisticsProcessingFacade = $statisticsProcessingFacade;
    }

    /**
     * @Route("/dashboard/")
     */
    public function dashboardAction()
    {
        $registeredInLastTwoWeeks = $this->statisticsFacade->getCustomersRegistrationsCountByDayInLastTwoWeeks();
        $registeredInLastTwoWeeksDates = $this->statisticsProcessingFacade->getDateTimesFormattedToLocaleFormat($registeredInLastTwoWeeks);
        $registeredInLastTwoWeeksCounts = $this->statisticsProcessingFacade->getCounts($registeredInLastTwoWeeks);
        $newOrdersCountByDayInLastTwoWeeks = $this->statisticsFacade->getNewOrdersCountByDayInLastTwoWeeks();
        $newOrdersInLastTwoWeeksDates = $this->statisticsProcessingFacade->getDateTimesFormattedToLocaleFormat($newOrdersCountByDayInLastTwoWeeks);
        $newOrdersInLastTwoWeeksCounts = $this->statisticsProcessingFacade->getCounts($newOrdersCountByDayInLastTwoWeeks);

        return $this->render(
            '@ShopsysShop/Admin/Content/Default/index.html.twig',
            [
                'registeredInLastTwoWeeksLabels' => $registeredInLastTwoWeeksDates,
                'registeredInLastTwoWeeksValues' => $registeredInLastTwoWeeksCounts,
                'newOrdersInLastTwoWeeksLabels' => $newOrdersInLastTwoWeeksDates,
                'newOrdersInLastTwoWeeksValues' => $newOrdersInLastTwoWeeksCounts,
            ]
        );
    }
}
