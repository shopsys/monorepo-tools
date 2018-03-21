<?php

namespace Shopsys\FrameworkBundle\Component\Grid;

use Shopsys\FrameworkBundle\Component\Router\Security\RouteCsrfProtector;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ActionColumn
{
    const TYPE_DELETE = 'delete';
    const TYPE_EDIT = 'edit';

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var RouteCsrfProtector
     */
    private $routeCsrfProtector;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $bindingRouteParams;

    /**
     * @var array
     */
    private $additionalRouteParams;

    /**
     * @var string|null
     */
    private $classAttribute;

    /**
     * @var string|null
     */
    private $confirmMessage;

    /**
     * @var bool
     */
    private $isAjaxConfirm;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Shopsys\FrameworkBundle\Component\Router\Security\RouteCsrfProtector $routeCsrfProtector
     * @param string $type
     * @param string $title
     * @param string $route
     * @param array $bindingRouteParams
     * @param array $additionalRouteParams
     */
    public function __construct(
        RouterInterface $router,
        RouteCsrfProtector $routeCsrfProtector,
        $type,
        $title,
        $route,
        array $bindingRouteParams,
        array $additionalRouteParams
    ) {
        $this->router = $router;
        $this->routeCsrfProtector = $routeCsrfProtector;
        $this->type = $type;
        $this->title = $title;
        $this->route = $route;
        $this->bindingRouteParams = $bindingRouteParams;
        $this->additionalRouteParams = $additionalRouteParams;
        $this->isAjaxConfirm = false;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getClassAttribute()
    {
        return $this->classAttribute;
    }

    /**
     * @return string|null
     */
    public function getConfirmMessage()
    {
        return $this->confirmMessage;
    }

    /**
     * @param string $classAttribute
     * @return \Shopsys\FrameworkBundle\Component\Grid\ActionColumn
     */
    public function setClassAttribute($classAttribute)
    {
        $this->classAttribute = $classAttribute;

        return $this;
    }

    /**
     * @param string $confirmMessage
     * @return \Shopsys\FrameworkBundle\Component\Grid\ActionColumn
     */
    public function setConfirmMessage($confirmMessage)
    {
        $this->confirmMessage = $confirmMessage;

        return $this;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Grid\ActionColumn
     */
    public function setAjaxConfirm()
    {
        $this->isAjaxConfirm = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAjaxConfirm()
    {
        return $this->isAjaxConfirm;
    }

    /**
     * @param array $row
     * @return string
     */
    public function getTargetUrl(array $row)
    {
        $parameters = $this->additionalRouteParams;

        foreach ($this->bindingRouteParams as $key => $sourceColumnName) {
            $parameters[$key] = Grid::getValueFromRowBySourceColumnName($row, $sourceColumnName);
        }

        if ($this->type === self::TYPE_DELETE) {
            $parameters[RouteCsrfProtector::CSRF_TOKEN_REQUEST_PARAMETER] = $this->routeCsrfProtector->getCsrfTokenByRoute($this->route);
        }

        return $this->router->generate($this->route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
