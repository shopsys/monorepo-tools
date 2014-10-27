<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\Grid\ArrayDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TranslationController extends Controller {

	/**
	 * @Route("/translation/list/")
	 */
	public function listAction() {
		$gridFactory = $this->get('ss6.shop.grid.factory');
		/* @var $gridFactory \SS6\ShopBundle\Model\Grid\GridFactory */

		$dataSource = new ArrayDataSource($this->loadData(), 'id');

		$grid = $gridFactory->create('translationList', $dataSource);

		$grid->addColumn('id', 'id', 'Konstanta');
		$grid->addColumn('cs', 'cs', 'Česky');
		$grid->addColumn('en', 'en', 'Anglicky');

		return $this->render('@SS6Shop/Admin/Content/Translation/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

	private function loadData() {
		$translator = $this->get('translator');
		/* @var $translator \SS6\ShopBundle\Component\Translator */

		$catalogueCs = $translator->getCalatogue('cs');
		$catalogueEn = $translator->getCalatogue('en');

		$data = array();
		foreach ($catalogueCs->all('messages') as $id => $translation) {
			$data[$id]['id'] = $id;
			$data[$id]['cs'] = $translation;
			$data[$id]['en'] = null;
		}

		foreach ($catalogueEn->all('messages') as $id => $translation) {
			$data[$id]['id'] = $id;
			if (!isset($data[$id]['cs'])) {
				$data[$id]['cs'] = null;
			}
			$data[$id]['en'] = $translation;
		}

		return $data;
	}

}
