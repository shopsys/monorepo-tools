<?php

namespace SS6\ShopBundle\Model\Product\Flag\Exception;

use SS6\ShopBundle\Model\Product\Flag\Exception\FlagException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FlagNotFoundException extends NotFoundHttpException implements FlagException {

}
