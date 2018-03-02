<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Currency\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CurrencyNotFoundException extends NotFoundHttpException implements CurrencyException
{
}
