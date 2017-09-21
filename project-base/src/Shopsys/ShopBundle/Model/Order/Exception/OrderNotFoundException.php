<?php

namespace Shopsys\ShopBundle\Model\Order\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderNotFoundException extends NotFoundHttpException implements OrderException
{
}
