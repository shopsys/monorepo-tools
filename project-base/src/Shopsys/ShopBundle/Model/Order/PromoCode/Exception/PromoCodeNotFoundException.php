<?php

namespace SS6\ShopBundle\Model\Order\PromoCode\Exception;

use SS6\ShopBundle\Model\Order\PromoCode\Exception\PromoCodeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoCodeNotFoundException extends NotFoundHttpException implements PromoCodeException {

}
