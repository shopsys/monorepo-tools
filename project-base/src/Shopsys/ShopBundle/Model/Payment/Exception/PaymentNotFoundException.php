<?php

namespace Shopsys\FrameworkBundle\Model\Payment\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentNotFoundException extends NotFoundHttpException implements PaymentException
{
}
