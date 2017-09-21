<?php

namespace Shopsys\ShopBundle\Component\Error\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class FakeHttpException extends HttpException implements ErrorException
{
}
