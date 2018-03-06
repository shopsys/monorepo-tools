<?php

namespace Shopsys\FrameworkBundle\Model\Transport\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransportNotFoundException extends NotFoundHttpException implements TransportException
{
}
