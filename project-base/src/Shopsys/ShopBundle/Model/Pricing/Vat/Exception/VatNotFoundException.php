<?php

namespace Shopsys\ShopBundle\Model\Pricing\Vat\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VatNotFoundException extends NotFoundHttpException implements VatException
{
}
