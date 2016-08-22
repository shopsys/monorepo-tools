<?php

namespace SS6\ShopBundle\Component\Error\Exception;

use SS6\ShopBundle\Component\Error\Exception\ErrorException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FakeHttpException extends HttpException implements ErrorException {

}
