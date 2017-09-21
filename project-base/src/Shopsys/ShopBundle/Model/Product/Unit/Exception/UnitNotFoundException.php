<?php

namespace Shopsys\ShopBundle\Model\Product\Unit\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UnitNotFoundException extends NotFoundHttpException implements UnitException
{
}
