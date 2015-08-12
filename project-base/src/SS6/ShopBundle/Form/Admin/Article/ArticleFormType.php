<?php

namespace SS6\ShopBundle\Form\Admin\Article;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Article\Article;
use SS6\ShopBundle\Model\Article\ArticleData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ArticleFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var string[]
	 */
	private $articlePlacementLocalizedNamesByName;

	/**
	 * @var \SS6\ShopBundle\Model\Article\Article|null
	 */
	private $article;

	/**
	 * @var \SS6\ShopBundle\Model\Article\Article|null
	 */
	private $defaultSeoMetaDescription;

	/**
	 * @param \SS6\ShopBundle\Component\Translation\Translator
	 * @param string[]
	 * @param \SS6\ShopBundle\Model\Article\Article|null $article
	 * @param string|null $defaultSeoMetaDescription
	 */
	public function __construct(
		Translator $translator,
		$articlePlacementLocalizedNamesByName,
		Article $article = null,
		$defaultSeoMetaDescription = null
	) {
		$this->translator = $translator;
		$this->articlePlacementLocalizedNamesByName = $articlePlacementLocalizedNamesByName;
		$this->article = $article;
		$this->defaultSeoMetaDescription = $defaultSeoMetaDescription;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'article_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::TEXT)
			->add('text', FormType::WYSIWYG, ['required' => true,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím text článku']),
				],
			])
			->add('seoTitle', FormType::TEXT, [
				'required' => false,
			])
			->add('seoMetaDescription', FormType::TEXTAREA, [
				'required' => false,
				'attr' => [
					'placeholder' => $this->defaultSeoMetaDescription,
				],
			])
			->add('urls', FormType::URL_LIST, [
				'route_name' => 'front_article_detail',
				'entity_id' => $this->article === null ? null : $this->article->getId(),
			])
			->add('save', FormType::SUBMIT);

		if ($this->article === null) {
			$builder
				->add('domainId', FormType::DOMAIN, ['required' => true])
				->add('placement', FormType::CHOICE, [
					'required' => true,
					'choices' => $this->articlePlacementLocalizedNamesByName,
					'placeholder' => $this->translator->trans('-- Vyberte umístění článku --'),
					'constraints' => [
						new Constraints\NotBlank(['message' => 'Prosím vyberte umístění článku']),
					],
				]);
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => ArticleData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
