<?php

namespace Shopsys\ShopBundle\Model\Order\Exception;

use Shopsys\ShopBundle\Model\Order\Exception\OrderException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderNotFoundException extends NotFoundHttpException implements OrderException {

}
