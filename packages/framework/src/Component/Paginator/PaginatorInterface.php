<?php

namespace Shopsys\FrameworkBundle\Component\Paginator;

interface PaginatorInterface
{
    public function getResult($page, $pageSize);

    public function getTotalCount();
}
