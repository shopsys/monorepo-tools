<?php

namespace SS6\ShopBundle\Model\Product\BestsellingProduct\Exception;

use SS6\ShopBundle\Model\Product\BestsellingProduct\Exception\BestsellingProductException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BestsellingProductNotFoundException extends NotFoundHttpException implements BestsellingProductException {

}
