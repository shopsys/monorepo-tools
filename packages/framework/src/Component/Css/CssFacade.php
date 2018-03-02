<?php

namespace Shopsys\FrameworkBundle\Component\Css;

class CssFacade
{
    /**
     * @var string
     */
    private $cssVersionFilepath;

    public function __construct($cssVersionFilepath)
    {
        $this->cssVersionFilepath = $cssVersionFilepath;
    }

    /**
     * @param string $cssVersion
     */
    public function setCssVersion($cssVersion)
    {
        file_put_contents($this->cssVersionFilepath, $cssVersion);
    }

    /**
     * @return string
     */
    public function getCssVersion()
    {
        if (!file_exists($this->cssVersionFilepath)) {
            $message = 'File with css version not found in ' . $this->cssVersionFilepath;
            throw new \Shopsys\FrameworkBundle\Component\Css\Exception\CssVersionFileNotFound($message);
        }

        return file_get_contents($this->cssVersionFilepath);
    }
}
