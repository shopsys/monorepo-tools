<?php

namespace Shopsys\ShopBundle\Model\Advert\Exception;

use Shopsys\ShopBundle\Model\Advert\Exception\AdvertException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertNotFoundException extends NotFoundHttpException implements AdvertException
{
}
