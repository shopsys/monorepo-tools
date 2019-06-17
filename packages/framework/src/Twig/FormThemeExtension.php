<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig_SimpleFunction;

class FormThemeExtension extends \Twig_Extension
{
    /** @access protected */
    const ADMIN_THEME = '@ShopsysFramework/Admin/Form/theme.html.twig';
    /** @access protected */
    const FRONT_THEME = '@ShopsysShop/Front/Form/theme.html.twig';

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('getDefaultFormTheme', [$this, 'getDefaultFormTheme']),
        ];
    }

    /**
     * @return string
     */
    public function getDefaultFormTheme()
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        if ($this->isAdmin($masterRequest->get('_controller'))) {
            return static::ADMIN_THEME;
        } else {
            return static::FRONT_THEME;
        }
    }

    /**
     * @param string $controller
     * @return bool
     */
    protected function isAdmin(string $controller) : bool
    {
        return strpos($controller, 'Shopsys\FrameworkBundle\Controller\Admin') === 0 ||
            strpos($controller, 'Shopsys\ShopBundle\Controller\Admin') === 0;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'form_theme';
    }
}
