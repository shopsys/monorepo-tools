<?php

namespace Shopsys\ShopBundle\Model\Category\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RootCategoryNotFoundException extends NotFoundHttpException implements CategoryException
{
}
