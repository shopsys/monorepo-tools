<?php

namespace Shopsys\ShopBundle\Model\Payment\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentPriceNotFoundException extends NotFoundHttpException implements PaymentException
{
}
