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
	public function createDeleteResponse($message, $route, $entityId) {
		return $this->templating->renderResponse('@SS6Shop/Admin/Content/ConfirmDelete/directDelete.html.twig', [
			'message' => $message,
			'route' => $route,
			'routeParams' => ['id' => $entityId],
		]);
	}

	/**
	 * @param string $message
	 * @param string $route
	 * @param mixed $entityId
	 * @param array $listOfNewEntities
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function createSetNewAndDeleteResponse($message, $route, $entityId, array $listOfNewEntities) {
		return $this->templating->renderResponse('@SS6Shop/Admin/Content/ConfirmDelete/setNewAndDelete.html.twig', [
			'message' => $message,
			'route' => $route,
			'entityId' => $entityId,
			'listOfNewEntities' => $listOfNewEntities,
		]);
	}
}
