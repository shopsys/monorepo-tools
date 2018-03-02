<?php

namespace Shopsys\FrameworkBundle\Model\Category\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryNotFoundException extends NotFoundHttpException implements CategoryException
{
}
