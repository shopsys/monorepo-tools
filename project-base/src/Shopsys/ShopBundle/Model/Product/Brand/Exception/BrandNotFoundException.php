<?php

namespace Shopsys\ShopBundle\Model\Product\Brand\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BrandNotFoundException extends NotFoundHttpException implements BrandException
{
}
