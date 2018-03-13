<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Controller\AdminBaseController;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\Security\Annotation\CsrfProtection;
use Shopsys\FrameworkBundle\Form\Admin\Category\CategoryFormType;
use Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb;
use Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem;
use Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CategoryController extends AdminBaseController
{
    const ALL_DOMAINS = 0;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\Breadcrumb
     */
    private $breadcrumb;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory
     */
    private $categoryDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    private $categoryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;

    public function __construct(
        CategoryFacade $categoryFacade,
        CategoryDataFactory $categoryDataFactory,
        Session $session,
        Domain $domain,
        Breadcrumb $breadcrumb
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->categoryDataFactory = $categoryDataFactory;
        $this->session = $session;
        $this->domain = $domain;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @Route("/category/edit/{id}", requirements={"id" = "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function editAction(Request $request, $id)
    {
        $category = $this->categoryFacade->getById($id);
        $categoryData = $this->categoryDataFactory->createFromCategory($category);

        $form = $this->createForm(CategoryFormType::class, $categoryData, [
            'category' => $category,
            'scenario' => CategoryFormType::SCENARIO_EDIT,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryFacade->edit($id, $categoryData);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Category<strong><a href="{{ url }}">{{ name }}</a></strong> was modified'),
                [
                    'name' => $category->getName(),
                    'url' => $this->generateUrl('admin_category_edit', ['id' => $category->getId()]),
                ]
            );
            return $this->redirectToRoute('admin_category_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        $this->breadcrumb->overrideLastItem(new MenuItem(t('Editing category - %name%', ['%name%' => $category->getName()])));

        return $this->render('@ShopsysFramework/Admin/Content/Category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/category/new/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function newAction(Request $request)
    {
        $categoryData = $this->categoryDataFactory->createDefault();

        $form = $this->createForm(CategoryFormType::class, $categoryData, [
            'category' => null,
            'scenario' => CategoryFormType::SCENARIO_CREATE,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryData = $form->getData();

            $category = $this->categoryFacade->create($categoryData);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Category <strong><a href="{{ url }}">{{ name }}</a></strong> created'),
                [
                    'name' => $category->getName(),
                    'url' => $this->generateUrl('admin_category_edit', ['id' => $category->getId()]),
                ]
            );

            return $this->redirectToRoute('admin_category_list');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlashTwig(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/Category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/list/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function listAction(Request $request)
    {
        if (count($this->domain->getAll()) > 1) {
            if ($request->query->has('domain')) {
                $domainId = (int)$request->query->get('domain');
            } else {
                $domainId = (int)$this->session->get('categories_selected_domain_id', self::ALL_DOMAINS);
            }
        } else {
            $domainId = self::ALL_DOMAINS;
        }

        if ($domainId !== self::ALL_DOMAINS) {
            try {
                $this->domain->getDomainConfigById($domainId);
            } catch (\Shopsys\FrameworkBundle\Component\Domain\Exception\InvalidDomainIdException $ex) {
                $domainId = self::ALL_DOMAINS;
            }
        }

        $this->session->set('categories_selected_domain_id', $domainId);

        if ($domainId === self::ALL_DOMAINS) {
            $categoryDetails = $this->categoryFacade->getAllCategoryDetails($request->getLocale());
        } else {
            $categoryDetails = $this->categoryFacade->getVisibleCategoryDetailsForDomain($domainId, $request->getLocale());
        }

        return $this->render('@ShopsysFramework/Admin/Content/Category/list.html.twig', [
            'categoryDetails' => $categoryDetails,
            'isForAllDomains' => ($domainId === self::ALL_DOMAINS),
        ]);
    }

    /**
     * @Route("/category/save-order/", methods={"post"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function saveOrderAction(Request $request)
    {
        $categoriesOrderingData = $request->get('categoriesOrderingData');

        $parentIdByCategoryId = [];
        foreach ($categoriesOrderingData as $categoryOrderingData) {
            $categoryId = (int)$categoryOrderingData['categoryId'];
            $parentId = $categoryOrderingData['parentId'] === '' ? null : (int)$categoryOrderingData['parentId'];
            $parentIdByCategoryId[$categoryId] = $parentId;
        }

        $this->categoryFacade->editOrdering($parentIdByCategoryId);

        return new Response('OK - dummy');
    }

    /**
     * @Route("/category/delete/{id}", requirements={"id" = "\d+"})
     * @CsrfProtection
     * @param int $id
     */
    public function deleteAction($id)
    {
        try {
            $fullName = $this->categoryFacade->getById($id)->getName();

            $this->categoryFacade->deleteById($id);

            $this->getFlashMessageSender()->addSuccessFlashTwig(
                t('Category <strong>{{ name }}</strong> deleted'),
                [
                    'name' => $fullName,
                ]
            );
        } catch (\Shopsys\FrameworkBundle\Model\Category\Exception\CategoryNotFoundException $ex) {
            $this->getFlashMessageSender()->addErrorFlash(t('Selected category doesn\'t exist.'));
        }

        return $this->redirectToRoute('admin_category_list');
    }

    public function listDomainTabsAction()
    {
        $domainId = $this->session->get('categories_selected_domain_id', self::ALL_DOMAINS);

        return $this->render('@ShopsysFramework/Admin/Content/Category/domainTabs.html.twig', [
            'domainConfigs' => $this->domain->getAll(),
            'selectedDomainId' => $domainId,
        ]);
    }

    /**
     * @Route("/category/branch/{domainId}/{id}", requirements={"domainId" = "\d+", "id" = "\d+"}, condition="request.isXmlHttpRequest()")
     * @param int $domainId
     * @param int $id
     */
    public function loadBranchJsonAction($domainId, $id)
    {
        $domainId = (int)$domainId;
        $id = (int)$id;

        $parentCategory = $this->categoryFacade->getById($id);
        $categories = $parentCategory->getChildren();

        $categoriesData = [];
        foreach ($categories as $category) {
            $categoriesData[] = [
                'id' => $category->getId(),
                'categoryName' => $category->getName(),
                'isVisible' => $category->getCategoryDomain($domainId)->isVisible(),
                'hasChildren' => $category->hasChildren(),
                'loadUrl' => $this->generateUrl('admin_category_loadbranchjson', [
                    'domainId' => $domainId,
                    'id' => $category->getId(),
                ]),
            ];
        }

        return $this->json($categoriesData);
    }
}
