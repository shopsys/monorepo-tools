<?php

namespace SS6\ShopBundle\Form;

use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Component\Setting\SettingValue;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WysiwygType extends AbstractTypeExtension {

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	public function __construct(Setting $setting) {
		$this->setting = $setting;
	}

	/**
	 * @inheritdoc
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$cssVersion = $this->setting->get(Setting::CSS_VERSION, SettingValue::DOMAIN_ID_COMMON);

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
