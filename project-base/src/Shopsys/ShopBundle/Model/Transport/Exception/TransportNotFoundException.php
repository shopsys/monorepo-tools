<?php

namespace Shopsys\ShopBundle\Model\Transport\Exception;

use Shopsys\ShopBundle\Model\Transport\Exception\TransportException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransportNotFoundException extends NotFoundHttpException implements TransportException {

}
