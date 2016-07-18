<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SplFileObject;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Model\Newsletter\NewsletterFacade;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Newsletter\NewsletterFacade
	 */
	private $newsletterFacade;

	public function __construct(NewsletterFacade $newsletterFacade) {
		$this->newsletterFacade = $newsletterFacade;
	}

	/**
	 * @Route("/newsletter/")
	 */
	public function indexAction() {
		return $this->render('@SS6Shop/Admin/Content/Newsletter/index.html.twig');
	}

	/**
	 * @Route("/newsletter/export-csv/")
	 */
	public function exportAction() {
		$response = new StreamedResponse();
		$response->headers->set('Content-Type', 'text/csv; charset=utf-8');
		$response->headers->set('Content-Disposition', 'attachment; filename="emails.csv"');
		$response->setCallback(function () {
			$this->streamCsvExport();
		});

		return $response;
	}

	private function streamCsvExport() {
		$output = new SplFileObject('php://output', 'w+');

		$emailsDataIterator = $this->newsletterFacade->getAllEmailsDataIterator();
		foreach ($emailsDataIterator as $emailData) {
			$email = $emailData[0]['email'];
			$fields = [$email];
			$output->fputcsv($fields, ';');
		}
	}

}
