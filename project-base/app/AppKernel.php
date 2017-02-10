<?php

use Shopsys\AutoServicesBundle\Kernel;
use Shopsys\Environment;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {

	public function registerBundles() {
		$bundles = array(
			new Bmatzner\JQueryBundle\BmatznerJQueryBundle(),
			new Bmatzner\JQueryUIBundle\BmatznerJQueryUIBundle(),
			new Craue\FormFlowBundle\CraueFormFlowBundle(),
			new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
			new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
			new FM\ElfinderBundle\FMElfinderBundle(),
			new Fp\JsFormValidatorBundle\FpJsFormValidatorBundle(),
			new Intaro\PostgresSearchBundle\IntaroPostgresSearchBundle(),
			new JMS\TranslationBundle\JMSTranslationBundle(),
			new Presta\SitemapBundle\PrestaSitemapBundle(),
			new Prezent\Doctrine\TranslatableBundle\PrezentDoctrineTranslatableBundle(),
			new RaulFraile\Bundle\LadybugBundle\RaulFraileLadybugBundle(),
			new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new ShopSys\MigrationBundle\ShopSysMigrationBundle(),
			new Shopsys\AutoServicesBundle\ShopsysAutoServicesBundle(),
			new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
			new Symfony\Bundle\AsseticBundle\AsseticBundle(),
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new Symfony\Bundle\MonologBundle\MonologBundle(),
			new Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
			new Symfony\Bundle\TwigBundle\TwigBundle(),
			new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
			new VasekPurchart\ConsoleErrorsBundle\ConsoleErrorsBundle(),
			new Ivory\CKEditorBundle\IvoryCKEditorBundle(), // has to be loaded after FrameworkBundle and TwigBundle
			new Shopsys\ShopBundle\ShopsysShopBundle(), // must be loaded as last, because translations must overwrite other bundles
		);

		if (in_array($this->getEnvironment(), array(Environment::ENVIRONMENT_DEVELOPMENT))) {
			$bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
			$bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
			$bundles[] = new Shopsys\GeneratorBundle\ShopsysGeneratorBundle();
			$bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
			$bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
		}

		return $bundles;
	}

	public function registerContainerConfiguration(LoaderInterface $loader) {
		foreach ($this->getConfigs() as $filename) {
			if (file_exists($filename) && is_readable($filename)) {
				$loader->load($filename);
			}
		}
	}

	/**
	 * @return string[]
	 */
	private function getConfigs() {
		$configs = [
			__DIR__ . '/config/parameters_common.yml',
			__DIR__ . '/config/parameters.yml',
			__DIR__ . '/config/paths.yml',
			__DIR__ . '/config/config.yml',
			__DIR__ . '/config/security.yml',
		];
		switch ($this->getEnvironment()) {
			case Environment::ENVIRONMENT_DEVELOPMENT:
				$configs[] = __DIR__ . '/config/config_dev.yml';
				break;
			case Environment::ENVIRONMENT_TEST:
				$configs[] = __DIR__ . '/config/parameters_test.yml';
				$configs[] = __DIR__ . '/config/config_test.yml';
				break;
		}

		return $configs;
	}
}
