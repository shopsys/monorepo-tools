<?php

namespace Shopsys\FrameworkBundle\Model\Country\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CountryNotFoundException extends NotFoundHttpException implements CountryException
{
}
