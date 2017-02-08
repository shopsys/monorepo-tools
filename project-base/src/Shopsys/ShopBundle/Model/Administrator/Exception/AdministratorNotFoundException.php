<?php

namespace SS6\ShopBundle\Model\Administrator\Exception;

use SS6\ShopBundle\Model\Administrator\Exception\AdministratorException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdministratorNotFoundException extends NotFoundHttpException implements AdministratorException {

}
