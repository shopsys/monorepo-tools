<?php

namespace SS6\ShopBundle\Model\Order\Status\Exception;

use SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderStatusNotFoundException extends NotFoundHttpException implements OrderStatusException {

}
