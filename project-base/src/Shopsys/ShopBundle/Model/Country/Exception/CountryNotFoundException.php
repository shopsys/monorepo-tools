<?php

namespace Shopsys\ShopBundle\Model\Country\Exception;

use Shopsys\ShopBundle\Model\Country\Exception\CountryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CountryNotFoundException extends NotFoundHttpException implements CountryException
{
}
