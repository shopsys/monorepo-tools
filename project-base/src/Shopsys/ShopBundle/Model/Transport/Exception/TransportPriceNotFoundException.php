<?php

namespace Shopsys\ShopBundle\Model\Transport\Exception;

use Shopsys\ShopBundle\Model\Transport\Exception\TransportException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransportPriceNotFoundException extends NotFoundHttpException implements TransportException
{

}
