<?php

namespace Shopsys\ShopBundle\Twig;

use Shopsys\ShopBundle\Model\Localization\Localization;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_SimpleFunction;

class LocalizationExtension extends \Twig_Extension
{

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @var \Symfony\Component\Asset\Packages
     */
    private $assetPackages;

    public function __construct(ContainerInterface $container, Packages $assetPackages)
    {
        $this->assetPackages = $assetPackages;

        // Twig extensions are loaded during assetic:dump command,
        // so they cannot be dependent on Domain service (dependency of Localization)
        $this->localization = $container->get('shopsys.shop.localization');
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('localeFlag', [$this, 'getLocaleFlagHtml'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string $locale
     * @return string
     */
    public function getLocaleFlagHtml($locale, $showTitle = true)
    {
        $src = $this->assetPackages->getUrl('assets/admin/images/flags/' . $locale . '.png');

        if ($showTitle) {
            $title = $this->getTitle($locale);
            $html = '<img src="' . htmlspecialchars($src, ENT_QUOTES)
                . '" alt="' . htmlspecialchars($locale, ENT_QUOTES)
                . '" title="' . htmlspecialchars($title, ENT_QUOTES) . '" width="16" height="11" />';
        } else {
            $html = '<img src="' . htmlspecialchars($src, ENT_QUOTES)
                . '" alt="' . htmlspecialchars($locale, ENT_QUOTES) . '" width="16" height="11" />';
        }

        return $html;
    }

    /**
     * @param string $locale
     * @return string
     */
    private function getTitle($locale)
    {
        try {
            $title = $this->localization->getLanguageName($locale);
        } catch (\Shopsys\ShopBundle\Model\Localization\Exception\InvalidLocaleException $e) {
            $title = '';
        }

        return $title;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'localization';
    }
}
