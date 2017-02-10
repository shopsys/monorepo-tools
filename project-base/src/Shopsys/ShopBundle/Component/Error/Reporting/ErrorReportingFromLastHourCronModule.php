<?php

namespace Shopsys\ShopBundle\Component\Error\Reporting;

use DateTime;
use Shopsys\Environment;
use Shopsys\ShopBundle\Component\Cron\CronModuleInterface;
use Shopsys\ShopBundle\Component\Error\Reporting\LogErrorReportingFacade;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Mail\MailerService;
use Shopsys\ShopBundle\Model\Mail\MessageData;
use Symfony\Bridge\Monolog\Logger;

class ErrorReportingFromLastHourCronModule implements CronModuleInterface
{

    const REPORT_ERRORS_FOR_LAST_SECONDS = 3600 + 300;
    const ROTATED_LOG_NAME = Environment::ENVIRONMENT_PRODUCTION;

    const FROM_EMAIL = 'errors@shopsys.cz';
    const FROM_NAME = 'Error reporting';

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * @var string|null
     */
    private $emailForErrorReporting;

    /**
     * @var \Shopsys\ShopBundle\Component\Error\Reporting\LogErrorReportingFacade
     */
    private $logErrorReportingFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailerService
     */
    private $mailerService;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @param string|null $emailForErrorReporting
     * @param \Shopsys\ShopBundle\Component\Error\Reporting\LogErrorReportingFacade $logErrorReportingFacade
     * @param \Shopsys\ShopBundle\Model\Mail\MailerService $mailerService
     * @param \Shopsys\ShopBundle\Component\Setting\Setting $setting
     */
    public function __construct(
        $emailForErrorReporting,
        LogErrorReportingFacade $logErrorReportingFacade,
        MailerService $mailerService,
        Setting $setting
    ) {
        $this->emailForErrorReporting = $emailForErrorReporting;
        $this->logErrorReportingFacade = $logErrorReportingFacade;
        $this->mailerService = $mailerService;
        $this->setting = $setting;
    }

    /**
     * @inheritDoc
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    public function run() {
        if ($this->emailForErrorReporting === null) {
            $this->logger->addInfo('Email for error reporting is not set');
            return;
        }

        $reportProblemFrom = new DateTime('-' . self::REPORT_ERRORS_FOR_LAST_SECONDS . ' seconds');
        if ($this->logErrorReportingFacade->existsLogEntryFromDateTime($reportProblemFrom, self::ROTATED_LOG_NAME)) {
            $this->logger->addInfo('Found new errors in logs');

            $messageData = $this->createErrorReportingMessageData();
            try {
                $this->mailerService->send($messageData);
                $this->logger->addInfo('Errors were reported');
            } catch (\Shopsys\ShopBundle\Model\Mail\Exception\SendMailFailedException $e) {
                $this->logger->addCritical('Error reporting failed: ' . $e->getMessage(), $e->getFailedRecipients());
            }
        } else {
            $this->logger->addInfo('Nothing to report');
        }
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Mail\MessageData
     */
    private function createErrorReportingMessageData() {
        $logsTail = $this->logErrorReportingFacade->getLogsTail(self::ROTATED_LOG_NAME);

        $subject = 'Error reporting from ' . $this->getEshopIdentifier();
        $body =
            '<h2>Error reporting from eshop \'' . htmlspecialchars($this->getEshopIdentifier()) . '\'</h2>'
            . '<h3>Last logs entries (may not contain all):</h3>'
            . '<code>' . nl2br(htmlspecialchars($logsTail)) . '</code>';

        return new MessageData(
            $this->emailForErrorReporting,
            null,
            $body,
            $subject,
            self::FROM_EMAIL,
            self::FROM_NAME
        );
    }

    /**
     * @return string
     */
    private function getEshopIdentifier() {
        $domainId = 1;
        return $this->setting->getForDomain(Setting::BASE_URL, $domainId);
    }
}
