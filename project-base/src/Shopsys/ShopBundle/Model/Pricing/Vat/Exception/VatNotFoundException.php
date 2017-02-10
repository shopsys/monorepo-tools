<?php

namespace Shopsys\ShopBundle\Model\Pricing\Vat\Exception;

use Shopsys\ShopBundle\Model\Pricing\Vat\Exception\VatException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VatNotFoundException extends NotFoundHttpException implements VatException {

}
