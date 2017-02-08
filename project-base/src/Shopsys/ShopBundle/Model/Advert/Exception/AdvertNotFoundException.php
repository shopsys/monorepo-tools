<?php

namespace SS6\ShopBundle\Model\Advert\Exception;

use SS6\ShopBundle\Model\Advert\Exception\AdvertException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertNotFoundException extends NotFoundHttpException implements AdvertException {

}
