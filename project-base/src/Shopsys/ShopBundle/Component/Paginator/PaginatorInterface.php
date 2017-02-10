<?php

namespace Shopsys\ShopBundle\Component\Paginator;

interface PaginatorInterface
{
    public function getResult($page, $pageSize);
    public function getTotalCount();
}
