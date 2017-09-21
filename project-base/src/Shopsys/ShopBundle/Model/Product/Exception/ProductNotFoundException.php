<?php

namespace Shopsys\ShopBundle\Model\Product\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductNotFoundException extends NotFoundHttpException implements ProductException
{
}
