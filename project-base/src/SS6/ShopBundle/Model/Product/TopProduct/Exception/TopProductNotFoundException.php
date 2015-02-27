<?php

namespace SS6\ShopBundle\Model\Product\TopProduct\Exception;

use SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TopProductNotFoundException extends NotFoundHttpException implements TopProductException {

}
