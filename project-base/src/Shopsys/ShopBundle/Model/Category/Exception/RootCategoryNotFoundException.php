<?php

namespace SS6\ShopBundle\Model\Category\Exception;

use SS6\ShopBundle\Model\Category\Exception\CategoryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RootCategoryNotFoundException extends NotFoundHttpException implements CategoryException {

}
