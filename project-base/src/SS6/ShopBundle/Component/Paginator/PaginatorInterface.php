<?php

namespace SS6\ShopBundle\Component\Paginator;

interface PaginatorInterface {
	public function getResult($page, $pageSize);
	public function getTotalCount();
}
