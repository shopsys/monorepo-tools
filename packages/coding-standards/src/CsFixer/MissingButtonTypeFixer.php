<?php

namespace ShopSys\CodingStandards\CsFixer;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

class MissingButtonTypeFixer implements FixerInterface, DefinedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Adds mandatory type attribute to <button> HTML tag.',
            [
                new CodeSample('<button/>'),
                new CodeSample('<button>label</button>'),
                new CodeSample("<button\n    class=\"btn\"\n/>"),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(SplFileInfo $file, Tokens $tokens)
    {
        $code = preg_replace_callback(
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
            $tokens->generateCode()
        );

        $tokens->setCode($code);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Shopsys/missing_button_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(SplFileInfo $file)
    {
        return preg_match('/\.html(?:\.twig)?$/ui', $file->getFilename()) === 1;
    }
}
