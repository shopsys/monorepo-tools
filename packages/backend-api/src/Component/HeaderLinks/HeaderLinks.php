<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Component\HeaderLinks;

/**
 * @experimental
 */
class HeaderLinks
{
    /**
     * @var array[] in format [link, rel]
     */
    protected $links = [];

    /**
     * @param string $link
     * @param string $rel
     * @return self
     */
    public function add(string $link, string $rel): self
    {
        $clone = clone $this;
        $clone->links[] = ['link' => $link, 'rel' => $rel];
        return $clone;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        $links = array_map(static function (array $link) {
            return sprintf('<%s>; rel="%s"', $link['link'], $link['rel']);
        }, $this->links);
        return implode(', ', $links);
    }
}
