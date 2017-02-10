<?php

namespace Shopsys\ShopBundle\Model\Order\Item\Exception;

use Shopsys\ShopBundle\Model\Order\Exception\OrderException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrdetItemNotFoundException extends NotFoundHttpException implements OrderException
{

}
