<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Config;

class DomainConfig
{
    const STYLES_DIRECTORY_DEFAULT = 'common';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $stylesDirectory;

    /**
     * @var string|null
     */
    protected $designId;

    /**
     * @param int $id
     * @param string $url
     * @param string $name
     * @param string $locale
     * @param string $stylesDirectory
     * @param null $designId
     */
    public function __construct($id, $url, $name, $locale, $stylesDirectory = self::STYLES_DIRECTORY_DEFAULT, $designId = null)
    {
        $this->id = $id;
        $this->url = $url;
        $this->name = $name;
        $this->locale = $locale;
        $this->stylesDirectory = $stylesDirectory;
        $this->designId = $designId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getStylesDirectory()
    {
        return $this->stylesDirectory;
    }

    /**
     * @return string|null
     */
    public function getDesignId()
    {
        return $this->designId;
    }

    /**
     * @return bool
     */
    public function isHttps()
    {
        return strpos($this->url, 'https://') === 0;
    }
}
