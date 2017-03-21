<?php

namespace Shopsys\ShopBundle\Component\ConfirmDelete;

use Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;

class ConfirmDeleteResponseFactory
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $templating;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector
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
    public function createDeleteResponse($message, $route, $entityId)
    {
        return $this->templating->renderResponse('@ShopsysShop/Admin/Content/ConfirmDelete/directDelete.html.twig', [
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
     * @param object[] $possibleReplacements
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createSetNewAndDeleteResponse($message, $route, $entityId, array $possibleReplacements)
    {
        foreach ($possibleReplacements as $object) {
            if (!is_object($object) || !method_exists($object, 'getName') || !method_exists($object, 'getId')) {
                $message = 'All items in argument 4 passed to ' . __METHOD__ . ' must be objects with methods getId and getName.';

                throw new \Shopsys\ShopBundle\Component\ConfirmDelete\Exception\InvalidEntityPassedException($message);
            }
        }

        return $this->templating->renderResponse('@ShopsysShop/Admin/Content/ConfirmDelete/setNewAndDelete.html.twig', [
            'message' => $message,
            'route' => $route,
            'entityId' => $entityId,
            'routeCsrfToken' => $this->routeCsrfProtector->getCsrfTokenByRoute($route),
            'possibleReplacements' => $possibleReplacements,
            'CSRF_TOKEN_REQUEST_PARAMETER' => RouteCsrfProtector::CSRF_TOKEN_REQUEST_PARAMETER,
        ]);
    }
}
