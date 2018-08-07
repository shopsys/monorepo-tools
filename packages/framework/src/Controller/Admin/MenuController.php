<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Shopsys\FrameworkBundle\Component\Domain\DomainFacade;

class MenuController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\DomainFacade
     */
    private $domainFacade;

    public function __construct(DomainFacade $domainFacade)
    {
        $this->domainFacade = $domainFacade;
    }

    public function menuAction()
    {
        return $this->render('@ShopsysFramework/Admin/Inline/Menu/menu.html.twig', [
            'domainConfigs' => $this->domainFacade->getAllDomainConfigs(),
        ]);
    }
}
