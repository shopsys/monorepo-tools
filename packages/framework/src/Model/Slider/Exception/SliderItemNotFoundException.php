<?php

namespace Shopsys\FrameworkBundle\Model\Slider\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SliderItemNotFoundException extends NotFoundHttpException implements SliderItemException
{
}
