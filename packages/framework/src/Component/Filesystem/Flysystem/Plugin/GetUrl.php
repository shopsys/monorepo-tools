<?php

namespace Shopsys\FrameworkBundle\Component\Filesystem\Flysystem\Plugin;

use Barryvdh\elFinderFlysystemDriver\Plugin\GetUrl as BaseGetUrl;

class GetUrl extends BaseGetUrl
{
    /**
     * @var mixed[]
     */
    protected $options;

    /**
     * @param mixed[] $options
     */
    public function __construct($options = [])
    {
        $this->options = $options;
    }

    /**
     * Get the URL using a `getUrl()` method on the adapter.
     *
     * @param  string $path
     * @return string
     */
    protected function getFromMethod($path)
    {
        if (isset($this->options['URL'])) {
            return $this->options['URL'] . str_replace($this->options['path'], '', $path);
        }

        return parent::getFromMethod($path);
    }
}
