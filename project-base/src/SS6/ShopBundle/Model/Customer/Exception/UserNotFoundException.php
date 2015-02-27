<?php

namespace SS6\ShopBundle\Model\Customer\Exception;

use SS6\ShopBundle\Model\Customer\Exception\CustomerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException implements CustomerException {

}
