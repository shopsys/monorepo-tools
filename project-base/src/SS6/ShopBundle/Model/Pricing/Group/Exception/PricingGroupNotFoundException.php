<?php

namespace SS6\ShopBundle\Model\Pricing\Group\Exception;

use SS6\ShopBundle\Model\Pricing\Group\Exception\PricingGroupException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PricingGroupNotFoundException extends NotFoundHttpException implements PricingGroupException {

}
