<?php

namespace SS6\ShopBundle\Model\Transport\Exception;

use SS6\ShopBundle\Model\Transport\Exception\TransportException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransportPriceNotFoundException extends NotFoundHttpException implements TransportException {

}
