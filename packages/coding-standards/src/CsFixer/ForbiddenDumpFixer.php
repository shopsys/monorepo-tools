<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\CsFixer;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class ForbiddenDumpFixer implements FixerInterface, DefinedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Removes forgotten dumps from twig',
            [
                new CodeSample('{{ d(123) }}'),
                new CodeSample('{{ dump(1234) }}'),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $code = preg_replace_callback(
            '@{{\s*(d|dump)\s*\(\s*.+?\s*\)\s*}}@imsu',
            function ($matches) {
                return '';
            },
            $tokens->generateCode()
        );

        $tokens->setCode($code);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Shopsys/forbidden_dump';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(SplFileInfo $file): bool
    {
        return preg_match('/\.(twig|html(?:\.twig)?)$/ui', $file->getFilename()) === 1;
    }
}
