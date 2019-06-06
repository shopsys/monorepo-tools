<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NoVarDumperExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('d', function () {
            }),
            new TwigFunction('dump', function () {
            }),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'no_var_dumper_extension';
    }
}
