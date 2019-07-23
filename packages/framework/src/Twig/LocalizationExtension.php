<?php

namespace Shopsys\FrameworkBundle\Twig;

use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Symfony\Component\Asset\Packages;
use Twig_SimpleFunction;

class LocalizationExtension extends \Twig_Extension
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    protected $localization;

    /**
     * @var \Symfony\Component\Asset\Packages
     */
    protected $assetPackages;

    /**
     * @var string
     */
    protected $webDir;

    /**
     * @param string $webDir
     * @param \Symfony\Component\Asset\Packages $assetPackages
     * @param \Shopsys\FrameworkBundle\Model\Localization\Localization $localization
     */
    public function __construct(string $webDir, Packages $assetPackages, Localization $localization)
    {
        $this->assetPackages = $assetPackages;
        $this->localization = $localization;
        $this->webDir = $webDir;
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
     * @param bool $showTitle
     * @return string
     */
    public function getLocaleFlagHtml($locale, $showTitle = true)
    {
        $src = $this->assetPackages->getUrl('bundles/shopsysframework/flags/' . $locale . '.png');

        if (file_exists($this->webDir . $src) === false) {
            return strtoupper($locale);
        }

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
    protected function getTitle($locale)
    {
        return $this->localization->getLanguageName($locale);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'localization';
    }
}
