<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Group\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PricingGroupNotFoundException extends NotFoundHttpException implements PricingGroupException
{
}
