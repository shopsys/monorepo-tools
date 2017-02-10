<?php

namespace Shopsys\ShopBundle\Model\Customer\Exception;

use Shopsys\ShopBundle\Model\Customer\Exception\CustomerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException implements CustomerException
{

}
