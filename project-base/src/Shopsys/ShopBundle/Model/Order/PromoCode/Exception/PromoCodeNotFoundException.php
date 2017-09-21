<?php

namespace Shopsys\ShopBundle\Model\Order\PromoCode\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoCodeNotFoundException extends NotFoundHttpException implements PromoCodeException
{
}
