<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplateFacade;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade;
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

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade
     */
    private $unitFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade
     */
    private $availabilityFacade;

    public function __construct(
        StatisticsFacade $statisticsFacade,
        StatisticsProcessingFacade $statisticsProcessingFacade,
        MailTemplateFacade $mailTemplateFacade,
        UnitFacade $unitFacade,
        Setting $setting,
        AvailabilityFacade $availabilityFacade
    ) {
        $this->statisticsFacade = $statisticsFacade;
        $this->statisticsProcessingFacade = $statisticsProcessingFacade;
        $this->mailTemplateFacade = $mailTemplateFacade;
        $this->unitFacade = $unitFacade;
        $this->setting = $setting;
        $this->availabilityFacade = $availabilityFacade;
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

        $this->addWarningMessagesOnDashboard();

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

    private function addWarningMessagesOnDashboard(): void
    {
        if ($this->mailTemplateFacade->existsTemplateWithEnabledSendingHavingEmptyBodyOrSubject()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(
                t('<a href="{{ url }}">Some required e-mail templates are not fully set.</a>'),
                [
                    'url' => $this->generateUrl('admin_mail_template'),
                ]
            );
        }

        if (empty($this->unitFacade->getAll())) {
            $this->getFlashMessageSender()->addErrorFlashTwig(
                t('<a href="{{ url }}">There are no units, you need to create some.</a>'),
                [
                    'url' => $this->generateUrl('admin_unit_list'),
                ]
            );
        }

        if ($this->setting->get(Setting::DEFAULT_UNIT) === 0) {
            $this->getFlashMessageSender()->addErrorFlashTwig(
                t('<a href="{{ url }}">Default unit is not set.</a>'),
                [
                    'url' => $this->generateUrl('admin_unit_list'),
                ]
            );
        }

        if (empty($this->availabilityFacade->getAll())) {
            $this->getFlashMessageSender()->addErrorFlashTwig(
                t('<a href="{{ url }}">There are no availabilities, you need to create some.</a>'),
                [
                    'url' => $this->generateUrl('admin_availability_list'),
                ]
            );
        }

        if ($this->setting->get(Setting::DEFAULT_AVAILABILITY_IN_STOCK) === 0) {
            $this->getFlashMessageSender()->addErrorFlashTwig(
                t('<a href="{{ url }}">Default product in stock availability is not set.</a>'),
                [
                    'url' => $this->generateUrl('admin_availability_list'),
                ]
            );
        }
    }
}
