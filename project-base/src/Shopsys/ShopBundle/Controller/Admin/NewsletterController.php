<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\ShopBundle\Model\Newsletter\NewsletterFacade;
use SplFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends AdminBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Newsletter\NewsletterFacade
     */
    private $newsletterFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    public function __construct(NewsletterFacade $newsletterFacade, AdminDomainTabsFacade $adminDomainTabsFacade)
    {
        $this->newsletterFacade = $newsletterFacade;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
    }

    /**
     * @Route("/newsletter/")
     */
    public function indexAction()
    {
        return $this->render('@ShopsysShop/Admin/Content/Newsletter/index.html.twig');
    }

    /**
     * @Route("/newsletter/export-csv/")
     */
    public function exportAction()
    {
        $response = new StreamedResponse();
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="emails.csv"');
        $response->setCallback(function () {
            $this->streamCsvExport($this->adminDomainTabsFacade->getSelectedDomainId());
        });

        return $response;
    }

    /**
     * @param int $domainId
     */
    private function streamCsvExport($domainId)
    {
        $output = new SplFileObject('php://output', 'w+');

        $emailsDataIterator = $this->newsletterFacade->getAllEmailsDataIteratorByDomainId($domainId);
        foreach ($emailsDataIterator as $emailData) {
            $email = $emailData[0]['email'];
            $createdAt = $emailData[0]['createdAt'];
            $fields = [$email, $createdAt];
            $output->fputcsv($fields, ';');
        }
    }
}
