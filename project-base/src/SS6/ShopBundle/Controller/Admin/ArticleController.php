<?php

namespace SS6\ShopBundle\Controller\Admin;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Article\ArticleFormType;
use SS6\ShopBundle\Model\Article\Article;
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

		$form = $this->createForm(new ArticleFormType());

		$articleData = array();

		if (!$form->isSubmitted()) {
			$articleRepository = $this->get('ss6.shop.article.article_repository');
			/* @var $articleRepository \SS6\ShopBundle\Model\Article\ArticleRepository */
			$article = $articleRepository->getById($id);

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
		} elseif ($form->isSubmitted()) {
			$article = $this->get('ss6.shop.article.article_repository')->getById($id);
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
		$source = new Entity(Article::class);

		$grid = $this->createGrid();
		$grid->setSource($source);

		$grid->setVisibleColumns(array('name'));
		$grid->setColumnsOrder(array('name'));
		$grid->getColumn('name')->setTitle('Název');
		$grid->setDefaultOrder('name', 'asc');

		return $grid->getGridResponse('@SS6Shop/Admin/Content/Article/list.html.twig');
	}

	/**
	 * @return \APY\DataGridBundle\Grid\Grid
	 */
	private function createGrid() {
		$grid = $this->get('grid');
		/* @var $grid \APY\DataGridBundle\Grid\Grid */

		$grid->hideFilters();
		$grid->setActionsColumnTitle('Akce');
		$grid->setLimits(array(20));
		$grid->setDefaultLimit(20);

		$detailRowAction = new RowAction('Upravit', 'admin_article_edit');
		$detailRowAction->setRouteParameters(array('id'));
		$detailRowAction->setAttributes(array('type' => 'edit'));
		$grid->addRowAction($detailRowAction);

		$deleteRowAction = new RowAction('Smazat', 'admin_article_delete', true);
		$deleteRowAction->setConfirmMessage('Opravdu si přejete článek smazat?');
		$deleteRowAction->setRouteParameters(array('id'));
		$deleteRowAction->setAttributes(array('type' => 'delete'));
		$grid->addRowAction($deleteRowAction);

		return $grid;
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
		} elseif ($form->isSubmitted()) {
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
