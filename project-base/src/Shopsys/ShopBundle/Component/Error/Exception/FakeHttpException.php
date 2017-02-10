<?php

namespace Shopsys\ShopBundle\Component\Error\Exception;

use Shopsys\ShopBundle\Component\Error\Exception\ErrorException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FakeHttpException extends HttpException implements ErrorException
{

}
