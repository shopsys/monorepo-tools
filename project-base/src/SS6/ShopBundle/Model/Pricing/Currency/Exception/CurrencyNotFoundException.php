<?php

namespace SS6\ShopBundle\Model\Pricing\Currency\Exception;

use SS6\ShopBundle\Model\Pricing\Currency\Exception\CurrencyException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CurrencyNotFoundException extends NotFoundHttpException implements CurrencyException {

}
