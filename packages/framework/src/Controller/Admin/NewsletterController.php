<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Grid\GridFactory;
use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use Shopsys\FrameworkBundle\Form\Admin\QuickSearch\QuickSearchFormType;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade;
use SplFileObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade
     */
    private $newsletterFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    private $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Grid\GridFactory
     */
    private $gridFactory;

    public function __construct(
        NewsletterFacade $newsletterFacade,
        AdminDomainTabsFacade $adminDomainTabsFacade,
        GridFactory $gridFactory
    ) {
        $this->newsletterFacade = $newsletterFacade;
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->gridFactory = $gridFactory;
    }

    /**
     * @Route("/newsletter/list/")
     */
    public function listAction(Request $request)
    {
        $quickSearchForm = $this->createForm(QuickSearchFormType::class, new QuickSearchFormData());
        $quickSearchForm->handleRequest($request);

        $queryBuilder = $this->newsletterFacade->getQueryBuilderForQuickSearch(
            $this->adminDomainTabsFacade->getSelectedDomainId(),
            $quickSearchForm->getData()
        );

        $dataSource = new QueryBuilderDataSource($queryBuilder, 'u.id');
        $grid = $this->gridFactory->create('customerList', $dataSource);
        $grid->enablePaging();

        $grid->addColumn('email', 'email', 'Email');
        $grid->addColumn('createdAt', 'createdAt', t('Subscribed at'));
        $grid->setDefaultOrder('email');
        $grid->addDeleteActionColumn('admin_newsletter_delete', ['id' => 'id'])
            ->setConfirmMessage(t('Do you really want to remove this subscriber?'));

        $grid->setTheme('@ShopsysFramework/Admin/Content/Newsletter/listGrid.html.twig');

        return $this->render(
            '@ShopsysFramework/Admin/Content/Newsletter/list.html.twig',
            [
                'quickSearchForm' => $quickSearchForm->createView(),
                'gridView' => $grid->createView(),
            ]
        );
    }

    /**
     * @Route("/newsletter/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction(int $id)
    {
        try {
            $email = $this->newsletterFacade->getNewsletterSubscriberById($id)->getEmail();

            $this->newsletterFacade->deleteById($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Subscriber <strong>{{ email }}</strong> deleted'),
                [
                    'email' => $email,
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Customer\Exception\UserNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected subscriber doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_newsletter_list');
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
