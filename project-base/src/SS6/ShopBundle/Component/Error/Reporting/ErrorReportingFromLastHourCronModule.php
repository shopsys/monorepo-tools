<?php

namespace SS6\ShopBundle\Component\Error\Reporting;

use DateTime;
use SS6\Environment;
use SS6\ShopBundle\Component\Cron\CronModuleInterface;
use SS6\ShopBundle\Component\Error\Reporting\LogErrorReportingFacade;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Model\Mail\MailerService;
use SS6\ShopBundle\Model\Mail\MessageData;
use Symfony\Bridge\Monolog\Logger;

class ErrorReportingFromLastHourCronModule implements CronModuleInterface {

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
	private $errorReportingToEmail;

	/**
	 * @var \SS6\ShopBundle\Component\Error\Reporting\LogErrorReportingFacade
	 */
	private $logErrorReportingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailerService
	 */
	private $mailerService;

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @param string|null $errorReportingToEmail
	 * @param \SS6\ShopBundle\Component\Error\Reporting\LogErrorReportingFacade $logErrorReportingFacade
	 * @param \SS6\ShopBundle\Model\Mail\MailerService $mailerService
	 * @param \SS6\ShopBundle\Component\Setting\Setting $setting
	 */
	public function __construct(
		$errorReportingToEmail,
		LogErrorReportingFacade $logErrorReportingFacade,
		MailerService $mailerService,
		Setting $setting
	) {
		$this->errorReportingToEmail = $errorReportingToEmail;
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
		$reportProblemFrom = new DateTime('-' . self::REPORT_ERRORS_FOR_LAST_SECONDS . ' seconds');
		if ($this->logErrorReportingFacade->existsLogEntryFromDateTime($reportProblemFrom, self::ROTATED_LOG_NAME)) {
			$this->logger->addInfo('Found new errors in logs');

			$messageData = $this->createErrorReportingMessageData();
			try {
				$this->mailerService->send($messageData);
				$this->logger->addInfo('Errors were reported');
			} catch (\SS6\ShopBundle\Model\Mail\Exception\SendMailFailedException $e) {
				$this->logger->addCritical('Error reporting failed: ' . $e->getMessage(), $e->getFailedRecipients());
			}
		} else {
			$this->logger->addInfo('Nothing to report');
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Mail\MessageData
	 */
	private function createErrorReportingMessageData() {
		$logsTail = $this->logErrorReportingFacade->getLogsTail(self::ROTATED_LOG_NAME);

		$subject = 'Error reporting from ' . $this->getEshopIdentifier();
		$body =
			'<h2>Error reporting from eshop \'' . htmlspecialchars($this->getEshopIdentifier()) . '\'</h2>'
			. '<h3>Last logs entries (may not contain all):</h3>'
			. '<code>' . nl2br(htmlspecialchars($logsTail)) . '</code>';

		return new MessageData(
			$this->errorReportingToEmail,
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
