<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl\Exception;

use SS6\ShopBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FriendlyUrlNotFoundException extends NotFoundHttpException implements FriendlyUrlException {

}
