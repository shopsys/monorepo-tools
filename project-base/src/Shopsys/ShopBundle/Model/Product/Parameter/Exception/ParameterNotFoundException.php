<?php

namespace SS6\ShopBundle\Model\Product\Parameter\Exception;

use SS6\ShopBundle\Model\Product\Parameter\Exception\ParameterException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ParameterNotFoundException extends NotFoundHttpException implements ParameterException {

}
