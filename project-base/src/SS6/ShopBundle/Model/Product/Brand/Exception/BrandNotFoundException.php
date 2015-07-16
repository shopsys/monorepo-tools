<?php

namespace SS6\ShopBundle\Model\Product\Brand\Exception;

use SS6\ShopBundle\Model\Product\Brand\Exception\BrandException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BrandNotFoundException extends NotFoundHttpException implements BrandException {

}
