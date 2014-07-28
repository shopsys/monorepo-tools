<?php

namespace SS6\ShopBundle\Model\Image\Exception;

use Exception;

class EntityFilenameMethodNotFoundException extends Exception implements ImageException {

	/**
	 * @var Object
	 */
	private $entity;

	/**
	 * @var string
	 */
	private $filenameMethodName;

	/**
	 * @param Object $entity
	 * @param string $filenameMethodName
	 * @param \Exception $previous
	 */
	public function __construct($entity, $filenameMethodName, Exception $previous = null) {
		$this->entity = $entity;
		$this->filenameMethodName = $filenameMethodName;

		$message = sprintf(
			'Not found method "%s" for get image filename for entity "%s".',
			$this->filenameMethodName,
			get_class($this->entity)
		);
		parent::__construct($message, 0, $previous);
	}

	/**
	 * @return Object
	 */
	public function getEntity() {
		return $this->entity;
	}

	/**
	 * @return string
	 */
	public function getFilenameMethodName() {
		return $this->filenameMethodName;
	}

}