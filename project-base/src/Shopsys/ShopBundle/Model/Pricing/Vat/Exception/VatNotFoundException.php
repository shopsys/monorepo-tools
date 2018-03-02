<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Vat\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VatNotFoundException extends NotFoundHttpException implements VatException
{
}
