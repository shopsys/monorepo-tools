<?php

namespace SS6\ShopBundle\Component\BaseObject;

class BaseObject implements \Iterator {

	public function __get($name) {
		$class = get_class($this);
		throw new \SS6\ShopBundle\Component\BaseObject\Exception\PropertyNotFoundException(
			'Cannot read non-existent property ' . $class . ' ::$' . $name
		);
	}

	public function __set($name, $value) {
		$class = get_class($this);
		throw new \SS6\ShopBundle\Component\BaseObject\Exception\PropertyNotFoundException(
			'Cannot set non-existent property ' . $class . ' ::$' . $name
		);
	}

	public function rewind() {
		$this->throwIteratorAccessException();
	}

	public function current() {
		$this->throwIteratorAccessException();
	}

	public function key() {
		$this->throwIteratorAccessException();
	}

	public function next() {
		$this->throwIteratorAccessException();
	}

	public function valid() {
		$this->throwIteratorAccessException();
	}

	private function throwIteratorAccessException() {
		$class = get_class($this);
		throw new \SS6\ShopBundle\Component\BaseObject\Exception\PropertyNotFoundException(
			'Cannot iterate object ' . $class
		);
	}
}
