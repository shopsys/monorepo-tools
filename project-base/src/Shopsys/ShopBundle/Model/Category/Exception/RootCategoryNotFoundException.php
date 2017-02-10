<?php

namespace Shopsys\ShopBundle\Model\Category\Exception;

use Shopsys\ShopBundle\Model\Category\Exception\CategoryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RootCategoryNotFoundException extends NotFoundHttpException implements CategoryException
{

}
