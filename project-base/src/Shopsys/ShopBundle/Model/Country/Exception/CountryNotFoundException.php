<?php

namespace SS6\ShopBundle\Model\Country\Exception;

use SS6\ShopBundle\Model\Country\Exception\CountryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CountryNotFoundException extends NotFoundHttpException implements CountryException {

}
