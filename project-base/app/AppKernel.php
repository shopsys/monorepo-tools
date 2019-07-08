<?php

use Shopsys\FrameworkBundle\Component\Environment\EnvironmentType;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    /**
     * @{inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
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
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Presta\SitemapBundle\PrestaSitemapBundle(),
            new Prezent\Doctrine\TranslatableBundle\PrezentDoctrineTranslatableBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Shopsys\FormTypesBundle\ShopsysFormTypesBundle(),
            new Shopsys\GoogleCloudBundle\ShopsysGoogleCloudBundle(),
            new Shopsys\MigrationBundle\ShopsysMigrationBundle(),
            new Shopsys\ProductFeed\HeurekaBundle\ShopsysProductFeedHeurekaBundle(),
            new Shopsys\ProductFeed\HeurekaDeliveryBundle\ShopsysProductFeedHeurekaDeliveryBundle(),
            new Shopsys\ProductFeed\ZboziBundle\ShopsysProductFeedZboziBundle(),
            new Shopsys\ProductFeed\GoogleBundle\ShopsysProductFeedGoogleBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Snc\RedisBundle\SncRedisBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
            new VasekPurchart\ConsoleErrorsBundle\ConsoleErrorsBundle(),
            new FOS\CKEditorBundle\FOSCKEditorBundle(), // has to be loaded after FrameworkBundle and TwigBundle
            new Joschi127\DoctrineEntityOverrideBundle\Joschi127DoctrineEntityOverrideBundle(),
            new Shopsys\FrameworkBundle\ShopsysFrameworkBundle(),
            new Shopsys\ReadModelBundle\ShopsysReadModelBundle(), // has to be loaded after ShopsysFrameworkBundle because it overrides Twig `image` function
            new Shopsys\ShopBundle\ShopsysShopBundle(), // must be loaded as last, because translations must overwrite other bundles
        ];

        if ($this->getEnvironment() === EnvironmentType::DEVELOPMENT) {
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
        }

        return $bundles;
    }

    /**
     * @{inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        foreach ($this->getConfigs() as $filename) {
            if (file_exists($filename) && is_readable($filename)) {
                $loader->load($filename);
            }
        }
    }

    /**
     * @return string[]
     */
    private function getConfigs()
    {
        $configs = [
            __DIR__ . '/config/directories.yml',
            __DIR__ . '/config/parameters_common.yml',
            __DIR__ . '/config/parameters.yml',
            __DIR__ . '/config/paths.yml',
            __DIR__ . '/config/config.yml',
        ];
        switch ($this->getEnvironment()) {
            case EnvironmentType::DEVELOPMENT:
                $configs[] = __DIR__ . '/config/config_dev.yml';
                break;
            case EnvironmentType::TEST:
                $configs[] = __DIR__ . '/config/parameters_test.yml';
                $configs[] = __DIR__ . '/config/config_test.yml';
                break;
        }

        if (file_exists(__DIR__ . '/../../parameters_monorepo.yml')) {
            $configs[] = __DIR__ . '/../../parameters_monorepo.yml';
        }

        if (file_exists(__DIR__ . '/config/parameters_version.yml')) {
            $configs[] = __DIR__ . '/config/parameters_version.yml';
        }

        return $configs;
    }

    /**
     * @{inheritdoc}
     */
    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * @{inheritdoc}
     */
    public function getCacheDir()
    {
        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    /**
     * @{inheritdoc}
     */
    public function getLogDir()
    {
        return dirname(__DIR__) . '/var/logs';
    }
}
