<?php

namespace Shopsys\ShopBundle\DependencyInjection\Compiler;

use SplFileInfo;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

class CustomTranslationsCompilerPass implements CompilerPassInterface
{
    const CUSTOM_TRANSLATIONS_DIR = 'src/Shopsys/ShopBundle/Resources/translations/custom';

    /**
     * @see \Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension::registerTranslatorConfiguration()
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container) {
        $customTranslationsDir = $container->getParameter('kernel.root_dir') . '/../' . self::CUSTOM_TRANSLATIONS_DIR;
        $translator = $container->findDefinition('translator.default');

        $finder = Finder::create()
            ->files()
            ->filter(function (SplFileInfo $file) {
                return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
            })
            ->in($customTranslationsDir);

        foreach ($finder as $file) {
            // filename is domain.locale.format
            list($domain, $locale, $format) = explode('.', $file->getBasename(), 3);
            $translator->addMethodCall('addResource', [$format, (string)$file, $locale, $domain]);
        }
    }
}
