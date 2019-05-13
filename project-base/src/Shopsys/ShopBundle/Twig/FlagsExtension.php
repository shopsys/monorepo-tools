<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\Twig;

use Shopsys\ReadModelBundle\Flag\FlagsProvider;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlagsExtension extends AbstractExtension
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    protected $templating;

    /**
     * @var \Shopsys\ReadModelBundle\Flag\FlagsProvider
     */
    protected $flagsProvider;

    /**
     * @param \Shopsys\ReadModelBundle\Flag\FlagsProvider $flagsProvider
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     */
    public function __construct(
        FlagsProvider $flagsProvider,
        EngineInterface $templating
    ) {
        $this->templating = $templating;
        $this->flagsProvider = $flagsProvider;
    }

    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('renderFlagsByIds', [$this, 'renderFlagsByIds'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param int[] $flagIds
     * @param string $classAddition
     * @return string
     */
    public function renderFlagsByIds(array $flagIds, string $classAddition = ''): string
    {
        return $this->templating->render(
            '@ShopsysShop/Front/Inline/Product/productFlags.html.twig',
            [
                'flags' => $this->flagsProvider->getFlagsByIds($flagIds),
                'classAddition' => $classAddition,
            ]
        );
    }
}
