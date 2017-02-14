<?php

namespace ShopSys\CodingStandards\CsFixer;

use SplFileInfo;
use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;

class MissingButtonTypeFixer extends AbstractFixer
{
    /**
     * @param \SplFileInfo $file
     * @param string       $content
     */
    public function fix(SplFileInfo $file, $content)
    {
        return preg_replace_callback(
            '@(<button\b)(.*?)(\s*/?>)@imsu',
            function ($matches) {
                $beginning = $matches[1];
                $attributes = $matches[2];
                $end = $matches[3];

                if (!preg_match('@(?:^|\s+)type=@', $attributes)) {
                    $attributes .= ' type="button"';
                }

                return $beginning . $attributes . $end;
            },
            $content
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Adds mandatory type attribute to <button> HTML tag';
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::NONE_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(SplFileInfo $file)
    {
        return preg_match('/\.html(?:\.twig)?$/ui', $file->getFilename()) === 1;
    }
}
