<?php

namespace SS6\ShopBundle\Model\PKGrid;

class Column {
	private $id;
	private $queryId;
	private $title;
	private $sortable;
	private $class;

	public function __construct($id, $queryId, $title, $sortable) {
		$this->id = $id;
		$this->queryId = $queryId;
		$this->title = $title;
		$this->sortable = $sortable;
	}

	public function getId() {
		return $this->id;
	}

	public function getQueryId() {
		return $this->queryId;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getSortable() {
		return $this->sortable;
	}

	public function getClass() {
		return $this->class;
	}

	public function setClass($class) {
		$this->class = $class;

		return $this;
	}

}
