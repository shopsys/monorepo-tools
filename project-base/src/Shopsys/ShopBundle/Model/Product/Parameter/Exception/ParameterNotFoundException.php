<?php

namespace Shopsys\ShopBundle\Model\Product\Parameter\Exception;

use Shopsys\ShopBundle\Model\Product\Parameter\Exception\ParameterException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ParameterNotFoundException extends NotFoundHttpException implements ParameterException
{

}
