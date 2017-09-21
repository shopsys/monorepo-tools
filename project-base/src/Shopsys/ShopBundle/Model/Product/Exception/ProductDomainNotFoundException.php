<?php

namespace Shopsys\ShopBundle\Model\Product\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductDomainNotFoundException extends NotFoundHttpException implements ProductException
{
}
