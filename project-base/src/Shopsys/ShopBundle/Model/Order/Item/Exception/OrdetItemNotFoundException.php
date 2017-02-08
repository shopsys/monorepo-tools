<?php

namespace SS6\ShopBundle\Model\Order\Item\Exception;

use SS6\ShopBundle\Model\Order\Exception\OrderException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrdetItemNotFoundException extends NotFoundHttpException implements OrderException {

}
