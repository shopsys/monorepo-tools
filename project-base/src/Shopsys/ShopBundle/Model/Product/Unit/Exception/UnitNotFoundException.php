<?php

namespace SS6\ShopBundle\Model\Product\Unit\Exception;

use SS6\ShopBundle\Model\Product\Unit\Exception\UnitException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UnitNotFoundException extends NotFoundHttpException implements UnitException {

}
