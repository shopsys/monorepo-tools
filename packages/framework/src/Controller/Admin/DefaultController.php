<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade;
use Shopsys\FrameworkBundle\Model\Statistics\StatisticsFacade;
use Shopsys\FrameworkBundle\Model\Statistics\StatisticsProcessingFacade;

class DefaultController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Statistics\StatisticsFacade
     */
    private $statisticsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Statistics\StatisticsProcessingFacade
     */
    private $statisticsProcessingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade
     */
    private $mailTemplateFacade;

    public function __construct(
        StatisticsFacade $statisticsFacade,
        StatisticsProcessingFacade $statisticsProcessingFacade,
        MailTemplateFacade $mailTemplateFacade
    ) {
        $this->statisticsFacade = $statisticsFacade;
        $this->statisticsProcessingFacade = $statisticsProcessingFacade;
        $this->mailTemplateFacade = $mailTemplateFacade;
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

        if ($this->mailTemplateFacade->existsTemplateWithEnabledSendingHavingEmptyBodyOrSubject()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(
                t('<a href="{{ url }}">Some required e-mail templates are not fully set.</a>'),
                [
                    'url' => $this->generateUrl('admin_mail_template'),
                ]
            );
        }

        return $this->render(
            '@ShopsysFramework/Admin/Content/Default/index.html.twig',
            [
                'registeredInLastTwoWeeksLabels' => $registeredInLastTwoWeeksDates,
                'registeredInLastTwoWeeksValues' => $registeredInLastTwoWeeksCounts,
                'newOrdersInLastTwoWeeksLabels' => $newOrdersInLastTwoWeeksDates,
                'newOrdersInLastTwoWeeksValues' => $newOrdersInLastTwoWeeksCounts,
            ]
        );
    }
}
