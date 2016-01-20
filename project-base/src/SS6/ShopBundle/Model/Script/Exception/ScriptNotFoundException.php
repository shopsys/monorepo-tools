<?php

namespace SS6\ShopBundle\Model\Script\Exception;

use SS6\ShopBundle\Model\Script\Exception\ScriptException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ScriptNotFoundException extends NotFoundHttpException implements ScriptException {

}
