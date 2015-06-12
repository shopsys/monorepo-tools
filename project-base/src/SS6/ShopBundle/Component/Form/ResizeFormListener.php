<?php

namespace SS6\ShopBundle\Component\Form;

use ArrayAccess;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Traversable;

/**
 * Symfony's ResizeFormListener modified to properly handle collections with view transformers.
 */
class ResizeFormListener implements EventSubscriberInterface {

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var bool
	 */
	private $allowAdd;

	/**
	 * @var bool
	 */
	private $allowDelete;

	/**
	 * @var bool
	 */
	private $deleteEmpty;

	/**
	 * @param string|null $type
	 * @param array $options
	 * @param bool $allowAdd
	 * @param bool $allowDelete
	 * @param bool $deleteEmpty
	 */
	public function __construct(
		$type,
		array $options = [],
		$allowAdd = false,
		$allowDelete = false,
		$deleteEmpty = false
	) {
		$this->type = $type;
		$this->allowAdd = $allowAdd;
		$this->allowDelete = $allowDelete;
		$this->options = $options;
		$this->deleteEmpty = $deleteEmpty;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents() {
		return [
			FormEvents::PRE_SET_DATA => 'preSetData',
			FormEvents::POST_SET_DATA => 'postSetData',
			FormEvents::PRE_SUBMIT => 'preSubmit',
			// (MergeCollectionListener, MergeDoctrineCollectionListener)
			FormEvents::SUBMIT => ['onSubmit', 50],
		];
	}

	/**
	 * Just for compatibility with original Symfony's ResizeFormListener,
	 * (CollectionType tests expect UnexpectedTypeException).
	 *
	 * @param \Symfony\Component\Form\FormEvent $event
	 */
	public function preSetData(FormEvent $event) {
		$data = $event->getData();

		if ($data === null) {
			$data = [];
		}

		if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
			throw new \Symfony\Component\Form\Exception\UnexpectedTypeException(
				$data,
				'array or (\Traversable and \ArrayAccess)'
			);
		}
	}

	/**
	 * Remove all form children and add them again to correspond to viewData.
	 * (In Symfony ResizeFormListener made with modelData)
	 *
	 * @param \Symfony\Component\Form\FormEvent $event
	 */
	public function postSetData(FormEvent $event) {
		$form = $event->getForm();
		$viewData = $form->getViewData();

		if ($viewData === null) {
			$viewData = [];
		}

		if (!is_array($viewData) && !($viewData instanceof Traversable && $viewData instanceof ArrayAccess)) {
			throw new \Symfony\Component\Form\Exception\UnexpectedTypeException(
				$viewData,
				'array or (\Traversable and \ArrayAccess)'
			);
		}

		// First remove all rows
		foreach ($form as $name => $child) {
			$form->remove($name);
		}

		$childOptions = $this->options;

		// Then add all rows again in the correct order
		foreach ($viewData as $name => $value) {
			$childOptions['property_path'] = '[' . $name . ']';
			$form->add($name, $this->type, $childOptions);
		}
	}

	/**
	 * Copy-pasted from original ResizeFormListener::preSubmit().
	 *
	 * @param \Symfony\Component\Form\FormEvent $event
	 */
	public function preSubmit(FormEvent $event) {
		$form = $event->getForm();
		$data = $event->getData();

		if ($data === null || $data === '') {
			$data = [];
		}

		if (!is_array($data) && !($data instanceof Traversable && $data instanceof ArrayAccess)) {
			throw new \Symfony\Component\Form\Exception\UnexpectedTypeException(
				$data,
				'array or (\Traversable and \ArrayAccess)'
			);
		}

		// Remove all empty rows
		if ($this->allowDelete) {
			foreach ($form as $name => $child) {
				if (!isset($data[$name])) {
					$form->remove($name);
				}
			}
		}

		// Add all additional rows
		if ($this->allowAdd) {
			$childOptions = $this->options;

			foreach ($data as $name => $value) {
				if (!$form->has($name)) {
					$childOptions['property_path'] = '[' . $name . ']';
					$form->add($name, $this->type, $childOptions);
				}
			}
		}
	}

	/**
	 * - Transform event's normData back to viewData (because viewData is not yet stored in form)
	 * - Remove empty children form form and viewData
	 * - Remove viewData which don't have child in form
	 * - Transform modified viewData back to normData
	 * (In Symfony this method works just with normData)
	 *
	 * @param \Symfony\Component\Form\FormEvent $event
	 */
	public function onSubmit(FormEvent $event) {
		$form = $event->getForm();
		$normData = $event->getData();
		$previousViewData = $form->getViewData();

		if ($normData === null) {
			$normData = [];
		}
		if (!is_array($normData) && !($normData instanceof Traversable && $normData instanceof ArrayAccess)) {
			throw new \Symfony\Component\Form\Exception\UnexpectedTypeException(
				$normData,
				'array or (\Traversable and \ArrayAccess)'
			);
		}

		if (null === $previousViewData) {
			$previousViewData = [];
		}
		if (!is_array($previousViewData) && !($previousViewData instanceof Traversable && $previousViewData instanceof ArrayAccess)) {
			throw new \Symfony\Component\Form\Exception\UnexpectedTypeException(
				$previousViewData,
				'array or (\Traversable and \ArrayAccess)'
			);
		}

		$viewData = $this->normToView($form, $normData);

		if ($this->deleteEmpty) {
			$viewData = $this->removeEmptyChildrenFromFormAndData($form, $viewData, $previousViewData);
		}

		// The data mapper only adds, but does not remove items, so do this
		// here
		if ($this->allowDelete) {
			$viewData = $this->removeDataItemsNotPresentInForm($viewData, $form);
		}

		$newNormData = $this->viewToNorm($form, $viewData);

		$event->setData($newNormData);
	}

	/**
	 * @param \Symfony\Component\Form\FormInterface $form
	 * @param mixed $viewData
	 * @param mixed $previousViewData
	 * @return mixed
	 */
	private function removeEmptyChildrenFromFormAndData(FormInterface $form, $viewData, $previousViewData) {
		foreach ($form as $name => $child) {
			$isNew = !isset($previousViewData[$name]);

			// $isNew can only be true if allowAdd is true, so we don't
			// need to check allowAdd again
			if ($child->isEmpty() && ($isNew || $this->allowDelete)) {
				unset($viewData[$name]);
				$form->remove($name);
			}
		}

		return $viewData;
	}

	/**
	 * @param mixed $viewData
	 * @param \Symfony\Component\Form\FormInterface $form
	 * @return mixed
	 */
	private function removeDataItemsNotPresentInForm($viewData, FormInterface $form) {
		$toDelete = [];

		foreach ($viewData as $name => $child) {
			if (!$form->has($name)) {
				$toDelete[] = $name;
			}
		}

		foreach ($toDelete as $name) {
			unset($viewData[$name]);
		}

		return $viewData;
	}

	/**
	 * Copy-pasted from Form::normToView()
	 *
	 * @param \Symfony\Component\Form\FormInterface $form
	 * @param mixed $value
	 * @return mixed
	 */
	private function normToView(FormInterface $form, $value) {
		// Scalar values should  be converted to strings to
		// facilitate differentiation between empty ("") and zero (0).
		// Only do this for simple forms, as the resulting value in
		// compound forms is passed to the data mapper and thus should
		// not be converted to a string before.
		if (!$form->getConfig()->getViewTransformers() && !$form->getConfig()->getCompound()) {
			return $value === null || is_scalar($value) ? (string)$value : $value;
		}

		foreach ($form->getConfig()->getViewTransformers() as $transformer) {
			$value = $transformer->transform($value);
		}

		return $value;
	}

	/**
	 * Copy-pasted from Form::viewToNorm()
	 *
	 * @param \Symfony\Component\Form\FormInterface $form
	 * @param string $value
	 * @return mixed
	 */
	private function viewToNorm(FormInterface $form, $value) {
		$transformers = $form->getConfig()->getViewTransformers();

		if (!$transformers) {
			return $value === '' ? null : $value;
		}

		for ($i = count($transformers) - 1; $i >= 0; --$i) {
			$value = $transformers[$i]->reverseTransform($value);
		}

		return $value;
	}

}
