<?php

namespace Shopsys\ShopBundle\Model\Order\PromoCode\Exception;

use Shopsys\ShopBundle\Model\Order\PromoCode\Exception\PromoCodeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoCodeNotFoundException extends NotFoundHttpException implements PromoCodeException
{
}
