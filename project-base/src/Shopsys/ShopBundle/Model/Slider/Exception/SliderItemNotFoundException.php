<?php

namespace Shopsys\ShopBundle\Model\Slider\Exception;

use Shopsys\ShopBundle\Model\Slider\Exception\SliderItemException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SliderItemNotFoundException extends NotFoundHttpException implements SliderItemException
{

}
