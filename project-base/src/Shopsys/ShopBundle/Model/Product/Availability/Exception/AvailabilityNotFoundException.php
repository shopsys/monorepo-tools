<?php

namespace Shopsys\ShopBundle\Model\Product\Availability\Exception;

use Shopsys\ShopBundle\Model\Product\Availability\Exception\AvailabilityException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AvailabilityNotFoundException extends NotFoundHttpException implements AvailabilityException
{

}
