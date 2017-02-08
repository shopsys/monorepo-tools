<?php

namespace SS6\ShopBundle\Model\Pricing\Vat\Exception;

use SS6\ShopBundle\Model\Pricing\Vat\Exception\VatException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VatNotFoundException extends NotFoundHttpException implements VatException {

}
