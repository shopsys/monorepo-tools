<?php

namespace SS6\ShopBundle\Model\Article\Exception;

use SS6\ShopBundle\Model\Article\Exception\ArticleException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleNotFoundException extends NotFoundHttpException implements ArticleException {

}
