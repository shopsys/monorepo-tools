<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Css\CssFacade;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WysiwygType extends AbstractTypeExtension {

	/**
	 * @var \SS6\ShopBundle\Component\Css\CssFacade
	 */
	private $cssFacade;

	public function __construct(CssFacade $cssFacade) {
		$this->cssFacade = $cssFacade;
	}

	/**
	 * @inheritdoc
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$cssVersion = $this->cssFacade->getCssVersion();

		$resolver->setDefaults([
			'config' => [
				'contentsCss' => [
					'assets/admin/styles/wysiwyg_' . $cssVersion . '.css',
				],
			],
		]);
	}

	/**
	 * @return string
	 */
	public function getExtendedType() {
		return 'ckeditor';
	}

}
