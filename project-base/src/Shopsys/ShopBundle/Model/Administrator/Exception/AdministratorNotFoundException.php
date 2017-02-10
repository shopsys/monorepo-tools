<?php

namespace Shopsys\ShopBundle\Model\Administrator\Exception;

use Shopsys\ShopBundle\Model\Administrator\Exception\AdministratorException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdministratorNotFoundException extends NotFoundHttpException implements AdministratorException
{
}
