<?php

namespace SS6\ShopBundle\Model\ConfirmDelete;

use Symfony\Bundle\TwigBundle\TwigEngine;

class ConfirmDeleteResponseFactory {

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
	 */
	private $templating;

	/**
	 * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
	 */
	public function __construct(TwigEngine $templating) {
		$this->templating = $templating;
	}

	/**
	 * @param string $message
	 * @param string $route
	 * @param mixed $entityId
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getDirectDeleteResponse($message, $route, $entityId) {
		return $this->templating->renderResponse('@SS6Shop/Admin/Content/ConfirmDelete/directDelete.html.twig', array(
			'message' => $message,
			'route' => $route,
			'routeParams' => array('id' => $entityId),
		));
	}

	/**
	 * @param string $message
	 * @param string $route
	 * @param mixed $entityId
	 * @param array $listOfNews
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getSetNewAndDeleteResponse($message, $route, $entityId, array $listOfNews) {
		return $this->templating->renderResponse('@SS6Shop/Admin/Content/ConfirmDelete/setNewAndDelete.html.twig', array(
			'message' => $message,
			'route' => $route,
			'entityId' => $entityId,
			'listOfNews' => $listOfNews,
		));
	}
}
