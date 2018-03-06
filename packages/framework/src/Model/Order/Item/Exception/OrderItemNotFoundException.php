<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item\Exception;

use Shopsys\FrameworkBundle\Model\Order\Exception\OrderException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderItemNotFoundException extends NotFoundHttpException implements OrderException
{
}
