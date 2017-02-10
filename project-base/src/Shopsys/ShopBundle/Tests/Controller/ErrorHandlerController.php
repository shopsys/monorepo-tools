<?php

namespace Shopsys\ShopBundle\Tests\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandlerController extends FrontBaseController
{

    /**
     * @Route("/error-handler/notice")
     */
    public function noticeAction() {
        $undefined[42];

        return new Response('');
    }
}
