<?php

namespace SS6\ShopBundle\Model\Order\Exception;

use SS6\ShopBundle\Model\Order\Exception\OrderException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderNotFoundException extends NotFoundHttpException implements OrderException {

}
