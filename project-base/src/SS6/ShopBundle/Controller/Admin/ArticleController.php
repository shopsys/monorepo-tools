<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Domain\SelectedDomain;
use SS6\ShopBundle\Component\Grid\GridFactory;
use SS6\ShopBundle\Component\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\Admin\Article\ArticleFormTypeFactory;
use SS6\ShopBundle\Model\Administrator\AdministratorGridFacade;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Article\ArticleDataFactory;
use SS6\ShopBundle\Model\Article\ArticleEditFacade;
use SS6\ShopBundle\Model\Article\ArticlePlacementList;
use SS6\ShopBundle\Model\TermsAndConditions\TermsAndConditionsFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade
	 */
	private $administratorGridFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleEditFacade
	 */
	private $articleEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleDataFactory
	 */
	private $articleDataFactory;

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Article\ArticleFormTypeFactory
	 */
	private $articleFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
	 */
	private $confirmDeleteResponseFactory;

	/**
	 * @var \SS6\ShopBundle\Model\TermsAndConditions\TermsAndConditionsFacade
	 */
	private $termsAndConditionsFacade;

	public function __construct(
		ArticleEditFacade $articleEditFacade,
		ArticleDataFactory $articleDataFactory,
		ArticleFormTypeFactory $articleFormTypeFactory,
		AdministratorGridFacade $administratorGridFacade,
		GridFactory $gridFactory,
		SelectedDomain $selectedDomain,
		Breadcrumb $breadcrumb,
		FriendlyUrlFacade $friendlyUrlFacade,
		Translator $translator,
		ConfirmDeleteResponseFactory $confirmDeleteResponseFactory,
		TermsAndConditionsFacade $termsAndConditionsFacade
	) {
		$this->articleEditFacade = $articleEditFacade;
		$this->articleDataFactory = $articleDataFactory;
		$this->articleFormTypeFactory = $articleFormTypeFactory;
		$this->administratorGridFacade = $administratorGridFacade;
		$this->gridFactory = $gridFactory;
		$this->selectedDomain = $selectedDomain;
		$this->breadcrumb = $breadcrumb;
		$this->friendlyUrlFacade = $friendlyUrlFacade;
		$this->translator = $translator;
		$this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
		$this->termsAndConditionsFacade = $termsAndConditionsFacade;
	}

	/**
	 * @Route("/article/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$article = $this->articleEditFacade->getById($id);
		$form = $this->createForm($this->articleFormTypeFactory->create(
			$this->selectedDomain->getId(),
			$article
		));

		$articleData = $this->articleDataFactory->createFromArticle($article);

		$form->setData($articleData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->transactional(
				function () use ($id, $articleData) {
					$this->articleEditFacade->edit($id, $articleData);
				}
			);

			$this->getFlashMessageSender()
				->addSuccessFlashTwig('Byl upraven článek <strong><a href="{{ url }}">{{ name }}</a></strong>', [
					'name' => $article->getName(),
					'url' => $this->generateUrl('admin_article_edit', ['id' => $article->getId()]),
				]);
			return $this->redirectToRoute('admin_article_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$this->breadcrumb->replaceLastItem(new MenuItem($this->translator->trans('Editace článku - ') . $article->getName()));

		return $this->render('@SS6Shop/Admin/Content/Article/edit.html.twig', [
			'form' => $form->createView(),
			'article' => $article,
		]);
	}

	/**
	 * @Route("/article/list/")
	 */
	public function listAction() {
		$gridTop = $this->getGrid(ArticlePlacementList::PLACEMENT_TOP_MENU);
		$gridFooter = $this->getGrid(ArticlePlacementList::PLACEMENT_FOOTER);
		$articlesCountOnSelectedDomain = $this->articleEditFacade->getAllArticlesCountByDomainId($this->selectedDomain->getId());

		return $this->render('@SS6Shop/Admin/Content/Article/list.html.twig', [
			'gridViewTop' => $gridTop->createView(),
			'gridViewFooter' => $gridFooter->createView(),
			'articlesCountOnSelectedDomain' => $articlesCountOnSelectedDomain,
		]);
	}

	/**
	 * @Route("/article/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$form = $this->createForm($this->articleFormTypeFactory->create($this->selectedDomain->getId()));

		$articleData = $this->articleDataFactory->createDefault();

		$form->setData($articleData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$articleData = $form->getData();

			$article = $this->transactional(
				function () use ($articleData) {
					return $this->articleEditFacade->create($articleData);
				}
			);

			$this->getFlashMessageSender()
				->addSuccessFlashTwig('Byl vytvořen článek <strong><a href="{{ url }}">{{ name }}</a></strong>', [
					'name' => $article->getName(),
					'url' => $this->generateUrl('admin_article_edit', ['id' => $article->getId()]),
				]);
			return $this->redirectToRoute('admin_article_list');
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlashTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Article/new.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/article/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$fullName = $this->articleEditFacade->getById($id)->getName();
			$this->transactional(
				function () use ($id) {
					$this->articleEditFacade->delete($id);
				}
			);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Článek <strong>{{ name }}</strong> byl smazán', [
				'name' => $fullName,
			]);
		} catch (\SS6\ShopBundle\Model\Article\Exception\ArticleNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolený článek neexistuje.');
		}

		return $this->redirectToRoute('admin_article_list');
	}

	/**
	 * @Route("/article/delete_confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		$article = $this->articleEditFacade->getById($id);
		if ($this->termsAndConditionsFacade->isArticleUsedAsTermsAndConditions($article)) {
			$message = $this->translator->trans(
				'Článek "%name%" je nastaven pro zobrazení obchodních podmínek.
				Toto nastavení bude ztraceno. Opravdu si jej přejete smazat?',
				['%name%' => $article->getName()]
			);
		} else {
			$message = 'Opravdu chcete odstranit tento článek?';
		}

		return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_article_delete', $id);
	}

	/**
	 * @Route("/article/save_ordering/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function saveOrderingAction(Request $request) {
		$this->transactional(
			function () use ($request) {
				$this->articleEditFacade->saveOrdering($request->get('rowIdsByGridId'));
			}
		);
		$responseData = ['success' => true];

		return new JsonResponse($responseData);
	}

	/**
	 * @param string $articlePlacement
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	private function getGrid($articlePlacement) {
		$queryBuilder = $this->articleEditFacade->getOrderedArticlesByDomainIdAndPlacementQueryBuilder(
			$this->selectedDomain->getId(),
			$articlePlacement
		);

		$dataSource = new QueryBuilderDataSource($queryBuilder, 'a.id');

		$gridId = $articlePlacement;
		$grid = $this->gridFactory->create($gridId, $dataSource);
		$grid->setDefaultOrder('position');

		$grid->addColumn('name', 'a.name', 'Název');

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_article_edit', ['id' => 'a.id']);
		$grid->addActionColumn('delete', 'Smazat', 'admin_article_deleteconfirm', ['id' => 'a.id'])
			->setAjaxConfirm();

		$grid->enableMultipleDragAndDrop();
		$grid->setTheme('@SS6Shop/Admin/Content/Article/listGrid.html.twig');

		return $grid;
	}

}
