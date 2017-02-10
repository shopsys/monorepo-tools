<?php

namespace Shopsys\ShopBundle\Model\Order\Status\Exception;

use Shopsys\ShopBundle\Model\Order\Status\Exception\OrderStatusException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderStatusNotFoundException extends NotFoundHttpException implements OrderStatusException {

}
