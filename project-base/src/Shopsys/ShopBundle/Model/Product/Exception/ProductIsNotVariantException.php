<?php

namespace Shopsys\ShopBundle\Model\Product\Exception;

use Shopsys\ShopBundle\Model\Product\Exception\ProductException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductIsNotVariantException extends NotFoundHttpException implements ProductException
{

}
