<?php

namespace Shopsys\ShopBundle\Model\Product\Flag\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FlagNotFoundException extends NotFoundHttpException implements FlagException
{
}
