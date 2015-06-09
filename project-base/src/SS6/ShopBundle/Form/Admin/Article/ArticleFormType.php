<?php

namespace SS6\ShopBundle\Form\Admin\Article;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Article\Article;
use SS6\ShopBundle\Model\Article\ArticleData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ArticleFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Article\Article|null
	 */
	private $article;

	/**
	 * @param \SS6\ShopBundle\Model\Article\Article|null $article
	 */
	public function __construct(Article $article = null) {
		$this->article = $article;
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
			->add('urls', FormType::URL_LIST, [
				'route_name' => 'front_article_detail',
				'entity_id' => $this->article === null ? null : $this->article->getId(),
			])
			->add('save', FormType::SUBMIT);

		if ($this->article === null) {
			$builder->add('domainId', FormType::DOMAIN, ['required' => true]);
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => ArticleData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
