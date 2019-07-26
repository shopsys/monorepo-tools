<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\Controller\Test;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Controller\Front\FrontBaseController;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandlerController extends FrontBaseController
{
    /**
     * @Route("/error-handler/notice")
     */
    public function noticeAction()
    {
        $undefined[42];

        return new Response('');
    }

    /**
     * @Route("/error-handler/exception")
     */
    public function exceptionAction()
    {
        throw new \Shopsys\ShopBundle\Controller\Test\ExpectedTestException('Expected exception');
    }

    /**
     * @Route("/error-handler/unknown-domain")
     */
    public function unknownDomainAction()
    {
        throw new \Shopsys\FrameworkBundle\Component\Domain\Exception\UnableToResolveDomainException('http://localhost:8000');
    }
}
