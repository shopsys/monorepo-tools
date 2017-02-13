<?php

namespace Shopsys\ShopBundle\Component\Router\FriendlyUrl\Exception;

use Shopsys\ShopBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FriendlyUrlNotFoundException extends NotFoundHttpException implements FriendlyUrlException
{
}
