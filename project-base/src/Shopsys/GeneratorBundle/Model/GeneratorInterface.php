<?php

namespace Shopsys\GeneratorBundle\Model;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

interface GeneratorInterface {

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 */
	public function buildForm(FormBuilderInterface $builder);

	/**
	 * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
	 * @param array $formData
	 * @return string
	 */
	public function generate(BundleInterface $bundle, array $formData);

}
