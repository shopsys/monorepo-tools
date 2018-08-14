<?php

namespace Shopsys\FrameworkBundle\Twig;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Domain\DomainFacade;
use Symfony\Component\Asset\Packages;
use Twig_SimpleFunction;

class DomainExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $domainImagesUrlPrefix;

    /**
     * @var \Symfony\Component\Asset\Packages
     */
    private $assetPackages;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\DomainFacade
     */
    private $domainFacade;

    public function __construct(
        $domainImagesUrlPrefix,
        Packages $assetPackages,
        Domain $domain,
        DomainFacade $domainFacade
    ) {
        $this->domainImagesUrlPrefix = $domainImagesUrlPrefix;
        $this->assetPackages = $assetPackages;
        $this->domain = $domain;
        $this->domainFacade = $domainFacade;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('getDomain', [$this, 'getDomain']),
            new Twig_SimpleFunction('getDomainName', [$this, 'getDomainNameById']),
            new Twig_SimpleFunction('domainIcon', [$this, 'getDomainIconHtml'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('isMultidomain', [$this, 'isMultidomain']),
        ];
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'domain';
    }

    /**
     * @param int $domainId
     * @return string
     */
    public function getDomainNameById($domainId)
    {
        return $this->getDomain()->getDomainConfigById($domainId)->getName();
    }

    /**
     * @param int $domainId
     * @param string $size
     * @return string
     */
    public function getDomainIconHtml($domainId, $size = 'normal')
    {
        $domainName = $this->getDomain()->getDomainConfigById($domainId)->getName();
        if ($this->domainFacade->existsDomainIcon($domainId)) {
            $src = $this->assetPackages->getUrl(sprintf('%s/%u.png', $this->domainImagesUrlPrefix, $domainId));

            return '
                <span class="in-image in-image--' . $size . '">
                    <span
                        class="in-image__in"
                    >
                        <img src="' . htmlspecialchars($src, ENT_QUOTES)
                        . '" alt="' . htmlspecialchars($domainId, ENT_QUOTES) . '"'
                        . ' title="' . htmlspecialchars($domainName, ENT_QUOTES) . '"/>
                    </span>
                </span>';
        } else {
            return '
                <span class="in-image in-image--' . $size . '">
                    <span
                        class="in-image__in in-image__in--' . $domainId . '"
                        title="' . htmlspecialchars($domainName, ENT_QUOTES) . '"
                    >' . $domainId . '</span>
                </span>
            ';
        }
    }

    /**
     * @return bool
     */
    public function isMultidomain()
    {
        return $this->getDomain()->isMultidomain();
    }
}
