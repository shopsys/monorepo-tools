<?php

namespace SS6\ShopBundle\Model\Slider\Exception;

use SS6\ShopBundle\Model\Slider\Exception\SliderItemException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SliderItemNotFoundException extends NotFoundHttpException implements SliderItemException {

}
