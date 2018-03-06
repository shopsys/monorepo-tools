<?php

namespace Shopsys\FrameworkBundle\Model\Product\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductVisibilityNotFoundException extends NotFoundHttpException implements ProductException
{
}
