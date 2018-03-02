<?php

namespace Shopsys\FrameworkBundle\Model\Payment\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentPriceNotFoundException extends NotFoundHttpException implements PaymentException
{
}
