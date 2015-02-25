<?php

namespace SS6\ShopBundle\Form\Admin\Seo;

use SS6\ShopBundle\Component\Constraints\NotInArray;
use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SeoSettingFormType extends AbstractType {

	/**
	 * @var string[]
	 */
	private $titlesOnOtherDomains;

	/**
	 * @var string[]
	 */
	private $titleAddOnsOnOtherDomains;

	/**
	 * @var string[]
	 */
	private $descriptionsOnOtherDomains;

	/**
	 * @param string[] $titlesOnOtherDomains
	 * @param string[] $titleAddOnsOnOtherDomains
	 * @param string[] $descriptionsOnOtherDomains
	 */
	public function __construct(
		array $titlesOnOtherDomains,
		array $titleAddOnsOnOtherDomains,
		array $descriptionsOnOtherDomains
	) {
		$this->titlesOnOtherDomains = $titlesOnOtherDomains;
		$this->titleAddOnsOnOtherDomains = $titleAddOnsOnOtherDomains;
		$this->descriptionsOnOtherDomains = $descriptionsOnOtherDomains;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'seo_setting';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('title', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new NotInArray([
						'array' => $this->titlesOnOtherDomains,
						'message' => 'Stejný titulek už je používán na jiné doméně',
					]),
				],
			])
			->add('titleAddOn', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new NotInArray([
						'array' => $this->titleAddOnsOnOtherDomains,
						'message' => 'Stejný doplněk titulku už je používán na jiné doméně',
					]),
				],
			])
			->add('metaDescription', FormType::TEXTAREA, [
				'required' => false,
				'constraints' => [
					new NotInArray([
						'array' => $this->descriptionsOnOtherDomains,
						'message' => 'Stejný popis už je používán na jiné doméně',
					]),
				],
			])
			->add('save', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
