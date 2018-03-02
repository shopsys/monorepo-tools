<?php

namespace Shopsys\FrameworkBundle\Model\Product\Parameter\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ParameterNotFoundException extends NotFoundHttpException implements ParameterException
{
}
