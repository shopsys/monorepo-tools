<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Form\Admin\Country\CountryFormType;
use Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider;
use Shopsys\FrameworkBundle\Model\Country\CountryDataFactory;
use Shopsys\FrameworkBundle\Model\Country\CountryFacade;
use Shopsys\FrameworkBundle\Model\Country\Grid\CountryGridFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CountryController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\Grid\CountryGridFactory
     */
    protected $countryGridFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryDataFactory
     */
    protected $countryDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryFacade
     */
    protected $countryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider
     */
    protected $breadcrumbOverrider;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\Grid\CountryGridFactory $countryGridFactory
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryDataFactory $countryDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryFacade $countryFacade
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\BreadcrumbOverrider $breadcrumbOverrider
     */
    public function __construct(
        CountryGridFactory $countryGridFactory,
        CountryDataFactory $countryDataFactory,
        CountryFacade $countryFacade,
        BreadcrumbOverrider $breadcrumbOverrider
    ) {
        $this->countryGridFactory = $countryGridFactory;
        $this->countryDataFactory = $countryDataFactory;
        $this->countryFacade = $countryFacade;
        $this->breadcrumbOverrider = $breadcrumbOverrider;
    }

    /**
     * @Route("/country/list/")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(): Response
    {
        $grid = $this->countryGridFactory->create();

        return $this->render('@ShopsysFramework/Admin/Content/Country/list.html.twig', [
            'gridView' => $grid->createView(),
        ]);
    }

    /**
     * @Route("/country/edit/{id}", requirements={"id" = "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id): Response
    {
        $country = $this->countryFacade->getById($id);
        $countryData = $this->countryDataFactory->createFromCountry($country);

        $form = $this->createForm(CountryFormType::class, $countryData, ['country' => $country]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->countryFacade->edit($id, $countryData);

            $this->getFlashMessageSender()
                ->addSuccessFlashTwig(
                    t('Country <strong><a href="{{ url }}">{{ name }}</a></strong> modified'),
                    [
                        'name' => $country->getName(),
                        'url' => $this->generateUrl('admin_country_edit', ['id' => $country->getId()]),
                    ]
                );

            return $this->redirectToRoute('admin_country_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumbOverrider->overrideLastItem(t('Editing country - %name%', ['%name%' => $country->getName()]));

        return $this->render('@ShopsysFramework/Admin/Content/Country/edit.html.twig', [
            'form' => $form->createView(),
            'country' => $country,
        ]);
    }

    /**
     * @Route("/country/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request): Response
    {
        $countryData = $this->countryDataFactory->create();

        $form = $this->createForm(CountryFormType::class, $countryData, ['country' => null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $country = $this->countryFacade->create($countryData);

            $this->getFlashMessageSender()
                ->addSuccessFlashTwig(
                    t('Country <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
                    [
                        'name' => $country->getName(),
                        'url' => $this->generateUrl('admin_country_edit', ['id' => $country->getId()]),
                    ]
                );

            return $this->redirectToRoute('admin_country_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Country/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
