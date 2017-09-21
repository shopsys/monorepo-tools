<?php

namespace Shopsys\ShopBundle\Model\Transport\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransportPriceNotFoundException extends NotFoundHttpException implements TransportException
{
}
