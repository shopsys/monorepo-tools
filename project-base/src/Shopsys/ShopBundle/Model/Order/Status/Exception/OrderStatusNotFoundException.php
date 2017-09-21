<?php

namespace Shopsys\ShopBundle\Model\Order\Status\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderStatusNotFoundException extends NotFoundHttpException implements OrderStatusException
{
}
