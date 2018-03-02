<?php

namespace Shopsys\FrameworkBundle\Twig;

use Shopsys\FrameworkBundle\Model\Module\ModuleFacade;
use Twig_Extension;
use Twig_SimpleFunction;

class ModuleExtension extends Twig_Extension
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Module\ModuleFacade
     */
    private $moduleFacade;

    public function __construct(ModuleFacade $moduleFacade)
    {
        $this->moduleFacade = $moduleFacade;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('isModuleEnabled', [$this, 'isModuleEnabled']),
        ];
    }

    /**
     * @param int $moduleName
     * @return string
     */
    public function isModuleEnabled($moduleName)
    {
        return $this->moduleFacade->isEnabled($moduleName);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'module';
    }
}
