<?php

namespace Shopsys\ShopBundle\Model\Script\Exception;

use Shopsys\ShopBundle\Model\Script\Exception\ScriptException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ScriptNotFoundException extends NotFoundHttpException implements ScriptException
{
}
