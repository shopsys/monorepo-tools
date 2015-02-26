<?php

namespace SS6\ShopBundle\Model\Product\Availability\Exception;

use SS6\ShopBundle\Model\Product\Availability\Exception\AvailabilityException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AvailabilityNotFoundException extends NotFoundHttpException implements AvailabilityException {

}
