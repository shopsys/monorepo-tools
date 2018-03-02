<?php

namespace Shopsys\FrameworkBundle\Model\Product\Flag\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FlagNotFoundException extends NotFoundHttpException implements FlagException
{
}
