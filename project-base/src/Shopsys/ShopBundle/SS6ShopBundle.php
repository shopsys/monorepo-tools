<?php

namespace SS6\ShopBundle;

use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\DependencyInjection\Compiler\CustomTranslationsCompilerPass;
use SS6\ShopBundle\Model\Security\Filesystem\FilemanagerAccess;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SS6ShopBundle extends Bundle {

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 */
	public function build(ContainerBuilder $container) {
		parent::build($container);

		$container->addCompilerPass(new CustomTranslationsCompilerPass());
	}

	public function boot() {
		parent::boot();

		$autoContainer = $this->container->get('ss6.auto_services.auto_container');
		/* @var $autoContainer \SS6\AutoServicesBundle\Compiler\AutoContainer */
		$filemanagerAccess = $autoContainer->get(FilemanagerAccess::class);
		FilemanagerAccess::injectSelf($filemanagerAccess);

		$translator = $autoContainer->get(Translator::class);
		Translator::injectSelf($translator);
	}

}
