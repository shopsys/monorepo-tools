<?php

namespace Shopsys\ShopBundle\Model\Payment\Exception;

use Shopsys\ShopBundle\Model\Payment\Exception\PaymentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentNotFoundException extends NotFoundHttpException implements PaymentException {

}
