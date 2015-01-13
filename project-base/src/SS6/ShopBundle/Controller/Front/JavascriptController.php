<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class JavascriptController extends Controller {

	/**
	 * @param string $filename
	 * @param string $path
	 * @return boolean
	 */
	private function isInPath($filename, $path) {
		return strpos($filename, $path) === 0;
	}

	/**
	 * @param string $dir
	 * @param string $file
	 */
	public function indexAction($dir, $file) {
		$jsTranslator = $this->get('ss6.shop.component.translation.js_translator');
		/* @var $jsTranslator \SS6\ShopBundle\Component\Translation\JsTranslator */

		$resourcesPath = realpath($this->container->getParameter('ss6.resources_dir'));
		$filename = realpath($resourcesPath . '/scripts/' . $dir . '/' . $file);

		if ($filename === false || !$this->isInPath($filename, $resourcesPath) || !is_file($filename)) {
			throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
		}

		$content = file_get_contents($filename);
		$translatedContent = $jsTranslator->translate($content);

		$response = new Response(
			$translatedContent,
			200,
			[
				'Content-Type' => 'text/javascript',
			]
		);

		return $response;
	}

}
