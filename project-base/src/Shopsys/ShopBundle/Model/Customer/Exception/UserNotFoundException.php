<?php

namespace Shopsys\ShopBundle\Model\Customer\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException implements CustomerException
{
}
