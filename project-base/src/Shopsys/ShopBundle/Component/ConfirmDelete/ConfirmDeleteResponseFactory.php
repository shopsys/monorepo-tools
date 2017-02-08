<?php

namespace SS6\ShopBundle\Component\ConfirmDelete;

use SS6\ShopBundle\Component\Router\Security\RouteCsrfProtector;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

class ConfirmDeleteResponseFactory {

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
	 */
	private $templating;

	/**
	 * @var \SS6\ShopBundle\Component\Router\Security\RouteCsrfProtector
	 */
	private $routeCsrfProtector;

	public function __construct(
		TwigEngine $templating,
		RouteCsrfProtector $routeCsrfProtector
	) {
		$this->templating = $templating;
		$this->routeCsrfProtector = $routeCsrfProtector;
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
			'routeParams' => [
				'id' => $entityId,
				RouteCsrfProtector::CSRF_TOKEN_REQUEST_PARAMETER => $this->routeCsrfProtector->getCsrfTokenByRoute($route),
			],
		]);
	}

	/**
	 * @param string $message
	 * @param string $route
	 * @param mixed $entityId
	 * @param \Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface $remainingEntitiesChoiceList
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function createSetNewAndDeleteResponse($message, $route, $entityId, ChoiceListInterface $remainingEntitiesChoiceList) {
		return $this->templating->renderResponse('@SS6Shop/Admin/Content/ConfirmDelete/setNewAndDelete.html.twig', [
			'message' => $message,
			'route' => $route,
			'entityId' => $entityId,
			'routeCsrfToken' => $this->routeCsrfProtector->getCsrfTokenByRoute($route),
			'remainingEntitiesChoiceList' => $remainingEntitiesChoiceList,
			'CSRF_TOKEN_REQUEST_PARAMETER' => RouteCsrfProtector::CSRF_TOKEN_REQUEST_PARAMETER,
		]);
	}
}
