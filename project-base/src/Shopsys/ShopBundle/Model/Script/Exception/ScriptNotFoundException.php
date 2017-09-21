<?php

namespace Shopsys\ShopBundle\Model\Script\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ScriptNotFoundException extends NotFoundHttpException implements ScriptException
{
}
