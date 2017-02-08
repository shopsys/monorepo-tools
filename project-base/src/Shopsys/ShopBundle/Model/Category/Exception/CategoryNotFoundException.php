<?php

namespace SS6\ShopBundle\Model\Category\Exception;

use SS6\ShopBundle\Model\Category\Exception\CategoryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryNotFoundException extends NotFoundHttpException implements CategoryException {

}
