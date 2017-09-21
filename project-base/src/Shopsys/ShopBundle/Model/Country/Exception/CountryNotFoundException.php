<?php

namespace Shopsys\ShopBundle\Model\Country\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CountryNotFoundException extends NotFoundHttpException implements CountryException
{
}
