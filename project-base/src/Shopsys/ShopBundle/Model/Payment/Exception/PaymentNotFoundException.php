<?php

namespace Shopsys\ShopBundle\Model\Payment\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentNotFoundException extends NotFoundHttpException implements PaymentException
{
}
