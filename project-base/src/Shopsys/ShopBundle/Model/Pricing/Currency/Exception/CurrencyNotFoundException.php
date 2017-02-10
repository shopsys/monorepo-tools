<?php

namespace Shopsys\ShopBundle\Model\Pricing\Currency\Exception;

use Shopsys\ShopBundle\Model\Pricing\Currency\Exception\CurrencyException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CurrencyNotFoundException extends NotFoundHttpException implements CurrencyException
{

}
