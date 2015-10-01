<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Domain\Domain;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class UrlListNewUrlType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('domain', FormType::DOMAIN);
		$builder->add('slug', FormType::TEXT, [
			'constraints' => [
				new Constraints\Regex('@^[\w_\-/]+$@'),
			],
		]);
		$builder->add('create', FormType::SUBMIT);
	}

	/**
	 * @param \Symfony\Component\Form\FormView $view
	 * @param \Symfony\Component\Form\FormInterface $form
	 * @param array $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options) {
		$view->vars['domainUrlsById'] = $this->getDomainUrlsIndexedById();
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'url_list_new_url_form';
	}

	/**
	 * @return string[domainId]
	 */
	private function getDomainUrlsIndexedById() {
		$domainUrlsById = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$domainUrlsById[$domainConfig->getId()] = $domainConfig->getUrl();
		}

		return $domainUrlsById;
	}

}
