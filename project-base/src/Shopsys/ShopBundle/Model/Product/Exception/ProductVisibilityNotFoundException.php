<?php

namespace SS6\ShopBundle\Model\Product\Exception;

use SS6\ShopBundle\Model\Product\Exception\ProductException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductVisibilityNotFoundException extends NotFoundHttpException implements ProductException {

}
