<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Form\Admin\Article\ArticleFormType;
use SS6\ShopBundle\Model\Administrator\AdministratorGridFacade;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Article\Article;
use SS6\ShopBundle\Model\Article\ArticleDataFactory;
use SS6\ShopBundle\Model\Article\ArticleEditFacade;
use SS6\ShopBundle\Model\Domain\SelectedDomain;
use SS6\ShopBundle\Model\Grid\GridFactory;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends BaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleEditFacade
	 */
	private $articleEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Article\ArticleDataFactory
	 */
	private $articleDataFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade
	 */
	private $administratorGridFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Grid\GridFactory
	 */
	private $gridFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	public function __construct(
		ArticleEditFacade $articleEditFacade,
		ArticleDataFactory $articleDataFactory,
		AdministratorGridFacade $administratorGridFacade,
		GridFactory $gridFactory,
		SelectedDomain $selectedDomain,
		Breadcrumb $breadcrumb,
		FriendlyUrlFacade $friendlyUrlFacade,
		Translator $translator
	) {
		$this->articleEditFacade = $articleEditFacade;
		$this->articleDataFactory = $articleDataFactory;
		$this->administratorGridFacade = $administratorGridFacade;
		$this->gridFactory = $gridFactory;
		$this->selectedDomain = $selectedDomain;
		$this->breadcrumb = $breadcrumb;
		$this->friendlyUrlFacade = $friendlyUrlFacade;
		$this->translator = $translator;
	}

	/**
	 * @Route("/article/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$article = $this->articleEditFacade->getById($id);
		$form = $this->createForm(new ArticleFormType($article));

		$articleData = $this->articleDataFactory->createFromArticle($article);

		$form->setData($articleData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->articleEditFacade->edit($id, $articleData);

			$this->getFlashMessageSender()
				->addSuccessFlashTwig('Byl upraven článek <strong><a href="{{ url }}">{{ name }}</a></strong>', [
					'name' => $article->getName(),
					'url' => $this->generateUrl('admin_article_edit', ['id' => $article->getId()]),
				]);
			return $this->redirect($this->generateUrl('admin_article_list'));
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
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */

		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('a')
			->from(Article::class, 'a')
			->where('a.domainId = :selectedDomainId')
			->setParameter('selectedDomainId', $this->selectedDomain->getId());
		$dataSource = new QueryBuilderDataSource($queryBuilder, 'a.id');

		$grid = $this->gridFactory->create('articleList', $dataSource);
		$grid->allowPaging();
		$grid->setDefaultOrder('name');

		$grid->addColumn('name', 'a.name', 'Název', true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_article_edit', ['id' => 'a.id']);
		$grid->addActionColumn('delete', 'Smazat', 'admin_article_delete', ['id' => 'a.id'])
			->setConfirmMessage('Opravdu chcete odstranit tento článek?');

		$grid->setTheme('@SS6Shop/Admin/Content/Article/listGrid.html.twig');

		$this->administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		return $this->render('@SS6Shop/Admin/Content/Article/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/article/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$form = $this->createForm(new ArticleFormType());

		$articleData = $this->articleDataFactory->createDefault();

		$form->setData($articleData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$articleData = $form->getData();

			$article = $this->articleEditFacade->create($articleData);

			$this->getFlashMessageSender()
				->addSuccessFlashTwig('Byl vytvořen článek <strong><a href="{{ url }}">{{ name }}</a></strong>', [
					'name' => $article->getName(),
					'url' => $this->generateUrl('admin_article_edit', ['id' => $article->getId()]),
				]);
			return $this->redirect($this->generateUrl('admin_article_list'));
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
	 * @param int $id
	 */
	public function deleteAction($id) {
		try {
			$fullName = $this->articleEditFacade->getById($id)->getName();
			$this->articleEditFacade->delete($id);

			$this->getFlashMessageSender()->addSuccessFlashTwig('Článek <strong>{{ name }}</strong> byl smazán', [
				'name' => $fullName,
			]);
		} catch (\SS6\ShopBundle\Model\Article\Exception\ArticleNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash('Zvolený článek neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_article_list'));
	}

}
