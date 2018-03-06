<?php

namespace Shopsys\FrameworkBundle\Model\Transport\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransportPriceNotFoundException extends NotFoundHttpException implements TransportException
{
}
