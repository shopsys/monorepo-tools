<?php

namespace Shopsys\ShopBundle\Model\Product\Brand\Exception;

use Shopsys\ShopBundle\Model\Product\Brand\Exception\BrandException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BrandNotFoundException extends NotFoundHttpException implements BrandException
{

}
