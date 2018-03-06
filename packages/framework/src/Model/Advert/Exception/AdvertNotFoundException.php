<?php

namespace Shopsys\FrameworkBundle\Model\Advert\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertNotFoundException extends NotFoundHttpException implements AdvertException
{
}
