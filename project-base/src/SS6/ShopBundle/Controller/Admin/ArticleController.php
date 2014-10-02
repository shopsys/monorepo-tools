<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Article\ArticleFormType;
use SS6\ShopBundle\Model\Article\Article;
use SS6\ShopBundle\Model\Article\ArticleData;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller {

	/**
	 * @Route("/article/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$articleRepository = $this->get('ss6.shop.article.article_repository');
		/* @var $articleRepository \SS6\ShopBundle\Model\Article\ArticleRepository */

		$article = $articleRepository->getById($id);
		$form = $this->createForm(new ArticleFormType());
		$articleData = new ArticleData();

		if (!$form->isSubmitted()) {
			$articleData->setFromEntity($article);
		}

		$form->setData($articleData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$articleData = $form->getData();

			$articleEditFacade = $this->get('ss6.shop.article.article_edit_facade');
			/* @var $articleEditFacade \SS6\ShopBundle\Model\Article\ArticleEditFacade */
			$article = $articleEditFacade->edit($id, $articleData);

			$flashMessageSender->addSuccessTwig('Byl upraven článek <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $article->getName(),
				'url' => $this->generateUrl('admin_article_edit', array('id' => $article->getId())),
			));
			return $this->redirect($this->generateUrl('admin_article_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */
		$breadcrumb->replaceLastItem(new MenuItem('Editace článku - ' . $article->getName()));

		return $this->render('@SS6Shop/Admin/Content/Article/edit.html.twig', array(
			'form' => $form->createView(),
			'article' => $article,
		));
	}

	/**
	 * @Route("/article/list/")
	 */
	public function listAction() {
		$administratorGridFacade = $this->get('ss6.shop.administrator.administrator_grid_facade');
		/* @var $administratorGridFacade \SS6\ShopBundle\Model\Administrator\AdministratorGridFacade */
		$administrator = $this->getUser();
		/* @var $administrator \SS6\ShopBundle\Model\Administrator\Administrator */
		$gridFactory = $this->get('ss6.shop.grid.factory');
		/* @var $gridFactory \SS6\ShopBundle\Model\Grid\GridFactory */
		$selectedDomain = $this->get('ss6.shop.domain.selected_domain');
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */

		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('a')
			->from(Article::class, 'a')
			->where('a.domainId = :selectedDomainId')
			->setParameter('selectedDomainId', $selectedDomain->getId());
		$dataSource = new QueryBuilderDataSource($queryBuilder);

		$grid = $gridFactory->create('articleList', $dataSource);
		$grid->allowPaging();
		$grid->setDefaultOrder('name');

		$grid->addColumn('name', 'a.name', 'Název', true);

		$grid->setActionColumnClassAttribute('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_article_edit', array('id' => 'a.id'));
		$grid->addActionColumn('delete', 'Smazat', 'admin_article_delete', array('id' => 'a.id'))
			->setConfirmMessage('Opravdu chcete odstranit tento článek?');

		$administratorGridFacade->restoreAndRememberGridLimit($administrator, $grid);

		return $this->render('@SS6Shop/Admin/Content/Article/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

	/**
	 * @Route("/article/new/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function newAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$selectedDomain = $this->get('ss6.shop.domain.selected_domain');
		/* @var $selectedDomain \SS6\ShopBundle\Model\Domain\SelectedDomain */

		$form = $this->createForm(new ArticleFormType());

		$articleData = new ArticleData();

		$form->setData($articleData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$articleData = $form->getData();
			$articleEditFacade = $this->get('ss6.shop.article.article_edit_facade');
			/* @var $articleEditFacade \SS6\ShopBundle\Model\Article\ArticleEditFacade */

			$article = $articleEditFacade->create($articleData);

			$flashMessageSender->addSuccessTwig('Byl vytvořen článek <strong><a href="{{ url }}">{{ name }}</a></strong>', array(
				'name' => $article->getName(),
				'url' => $this->generateUrl('admin_article_edit', array('id' => $article->getId())),
			));
			return $this->redirect($this->generateUrl('admin_article_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorTwig('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Article/new.html.twig', array(
			'form' => $form->createView(),
			'selectedDomainId' => $selectedDomain->getId(),
		));
	}

	/**
	 * @Route("/article/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$articleRepository = $this->get('ss6.shop.article.article_repository');
		/* @var $articleRepository \SS6\ShopBundle\Model\Article\ArticleRepository */

		$fullName = $articleRepository->getById($id)->getName();
		$this->get('ss6.shop.article.article_edit_facade')->delete($id);

		$flashMessageSender->addSuccessTwig('Článek <strong>{{ name }}</strong> byl smazán', array(
			'name' => $fullName,
		));
		return $this->redirect($this->generateUrl('admin_article_list'));
	}

}
