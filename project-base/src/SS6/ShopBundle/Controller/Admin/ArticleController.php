<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Article\ArticleFormType;
use SS6\ShopBundle\Model\Article\Article;
use SS6\ShopBundle\Model\PKGrid\PKGrid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller {

	/**
	 * @Route("/article/edit/{id}", requirements={"id" = "\d+"})
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function editAction(Request $request, $id) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */
		$articleRepository = $this->get('ss6.shop.article.article_repository');
		/* @var $articleRepository \SS6\ShopBundle\Model\Article\ArticleRepository */

		$article = $articleRepository->getById($id);
		$form = $this->createForm(new ArticleFormType());
		$articleData = array();

		if (!$form->isSubmitted()) {
			$articleData['id'] = $article->getId();
			$articleData['name'] = $article->getName();
			$articleData['text'] = $article->getText();
		}

		$form->setData($articleData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$articleData = $form->getData();

			$articleEditFacade = $this->get('ss6.shop.article.article_edit_facade');
			/* @var $articleEditFacade \SS6\ShopBundle\Model\Article\ArticleEditFacade */
			$article = $articleEditFacade->edit(
				$id,
				$articleData['name'],
				$articleData['text']
			);

			$flashMessage->addSuccess('Byl upraven článek ' . $article->getName());
			return $this->redirect($this->generateUrl('admin_article_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

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

		$queryBuilder = $this->getDoctrine()->getManager()->createQueryBuilder();
		$queryBuilder
			->select('a')
			->from(Article::class, 'a');

		$grid = new PKGrid(
			'articleList',
			$this->get('request_stack'),
			$this->get('router'),
			$this->get('twig')
		);
		$grid->allowPaging();
		$grid->setDefaultOrder('name');
		$grid->setQueryBuilder($queryBuilder, 'a.id');

		$grid->addColumn('name', 'a.name', 'Název', true);

		$grid->setActionColumnClass('table-col table-col-10');
		$grid->addActionColumn('edit', 'Upravit', 'admin_article_edit', array('id' => 'id'));
		$grid->addActionColumn('delete', 'Smazat', 'admin_article_delete', array('id' => 'id'))
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
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$form = $this->createForm(new ArticleFormType());

		$articleData = array();

		$form->setData($articleData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$articleData = $form->getData();
			$articleEditFacade = $this->get('ss6.shop.article.article_edit_facade');
			/* @var $articleEditFacade \SS6\ShopBundle\Model\Article\ArticleEditFacade */

			$article = $articleEditFacade->create(
				$articleData['name'],
				$articleData['text']
			);

			$flashMessage->addSuccess('Byl vytvořen článek ' . $article->getName());
			return $this->redirect($this->generateUrl('admin_article_list'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Admin/Content/Article/new.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * @Route("/article/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessage = $this->get('ss6.shop.flash_message.admin');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$articleRepository = $this->get('ss6.shop.article.article_repository');
		/* @var $articleRepository \SS6\ShopBundle\Model\Article\ArticleRepository */

		$fullName = $articleRepository->getById($id)->getName();
		$this->get('ss6.shop.article.article_edit_facade')->delete($id);
		$flashMessage->addSuccess('Článek ' . $fullName . ' byl smazán');

		return $this->redirect($this->generateUrl('admin_article_list'));
	}

}
