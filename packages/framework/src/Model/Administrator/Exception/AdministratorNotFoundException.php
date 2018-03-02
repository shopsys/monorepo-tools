<?php

namespace Shopsys\FrameworkBundle\Model\Administrator\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdministratorNotFoundException extends NotFoundHttpException implements AdministratorException
{
}
