<?php

namespace Shopsys\ShopBundle\Model\Administrator\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdministratorNotFoundException extends NotFoundHttpException implements AdministratorException
{
}
