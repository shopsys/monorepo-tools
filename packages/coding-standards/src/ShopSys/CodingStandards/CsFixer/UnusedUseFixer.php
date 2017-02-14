<?php

namespace ShopSys\CodingStandards\CsFixer;

use SplFileInfo;
use Symfony\CS\AbstractFixer;
use Symfony\CS\FixerInterface;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Original PHP CS UnusedUseFixer modified to not remove uses in same namespace.
 */
class UnusedUseFixer extends AbstractFixer
{
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
    public function getName()
    {
        return 'shopsys_unused_use';
    }

    /**
     * {@inheritdoc].
     */
    public function fix(SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $namespaceDeclarations = $this->getNamespaceDeclarations($tokens);
        $useDeclarationsIndexes = $tokens->getImportUseIndexes();
        $useDeclarations = $this->getNamespaceUseDeclarations($tokens, $useDeclarationsIndexes);
        $contentWithoutUseDeclarations = $this->generateCodeWithoutPartials(
            $tokens,
            array_merge($namespaceDeclarations, $useDeclarations)
        );
        $useUsages = $this->detectUseUsages($contentWithoutUseDeclarations, $useDeclarations);

        $this->removeUnusedUseDeclarations($tokens, $useDeclarations, $useUsages);

        return $tokens->generateCode();
    }

    private function detectUseUsages($content, array $useDeclarations)
    {
        $usages = [];

        foreach ($useDeclarations as $shortName => $useDeclaration) {
            $usages[$shortName] = (bool)preg_match('/\b' . preg_quote($shortName) . '\b/i', $content);
        }

        return $usages;
    }

    private function generateCodeWithoutPartials(Tokens $tokens, array $partials)
    {
        $content = '';

        foreach ($tokens as $index => $token) {
            $allowToAppend = true;

            foreach ($partials as $partial) {
                if ($partial['declarationStart'] <= $index && $index <= $partial['declarationEnd']) {
                    $allowToAppend = false;
                    break;
                }
            }

            if ($allowToAppend) {
                $content .= $token->getContent();
            }
        }

        return $content;
    }

    private function getNamespaceDeclarations(Tokens $tokens)
    {
        $namespaces = [];

        foreach ($tokens as $index => $token) {
            if (T_NAMESPACE !== $token->getId()) {
                continue;
            }

            $declarationEndIndex = $tokens->getNextTokenOfKind($index, [';', '{']);

            $namespaces[] = [
                'content' => trim($tokens->generatePartialCode($index + 1, $declarationEndIndex - 1)),
                'declarationStart' => $index,
                'declarationEnd' => $declarationEndIndex,
            ];
        }

        return $namespaces;
    }

    private function getNamespaceUseDeclarations(Tokens $tokens, array $useIndexes)
    {
        $uses = [];

        foreach ($useIndexes as $index) {
            $declarationEndIndex = $tokens->getNextTokenOfKind($index, [';']);
            $declarationContent = $tokens->generatePartialCode($index + 1, $declarationEndIndex - 1);

            // ignore multiple use statements like: `use BarB, BarC as C, BarD;`
            // that should be split into few separate statements
            if (false !== strpos($declarationContent, ',')) {
                continue;
            }

            $declarationParts = preg_split('/\s+as\s+/i', $declarationContent);

            if (1 === count($declarationParts)) {
                $fullName = $declarationContent;
                $declarationParts = explode('\\', $fullName);
                $shortName = end($declarationParts);
                $aliased = false;
            } else {
                $fullName = $declarationParts[0];
                $shortName = $declarationParts[1];
                $declarationParts = explode('\\', $fullName);
                $aliased = $shortName !== end($declarationParts);
            }

            $shortName = trim($shortName);

            $uses[$shortName] = [
                'shortName' => $shortName,
                'fullName' => trim($fullName),
                'aliased' => $aliased,
                'declarationStart' => $index,
                'declarationEnd' => $declarationEndIndex,
            ];
        }

        return $uses;
    }

    private function removeUnusedUseDeclarations(Tokens $tokens, array $useDeclarations, array $useUsages)
    {
        foreach ($useDeclarations as $shortName => $useDeclaration) {
            if (!$useUsages[$shortName]) {
                $this->removeUseDeclaration($tokens, $useDeclaration);
            }
        }
    }

    private function removeUseDeclaration(Tokens $tokens, array $useDeclaration)
    {
        for ($index = $useDeclaration['declarationStart']; $index <= $useDeclaration['declarationEnd']; ++$index) {
            $tokens[$index]->clear();
        }

        $token = $tokens[$useDeclaration['declarationStart'] - 1];

        if ($token->isWhitespace()) {
            $token->setContent(rtrim($token->getContent(), " \t"));
        }

        if (!isset($tokens[$useDeclaration['declarationEnd'] + 1])) {
            return;
        }

        $token = $tokens[$useDeclaration['declarationEnd'] + 1];

        if ($token->isWhitespace()) {
            $content = ltrim($token->getContent(), " \t");

            if ($content && "\n" === $content[0]) {
                $content = substr($content, 1);
            }

            $token->setContent($content);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the MultipleUseFixer
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        // some fixtures are auto-generated by Symfony and may contain unused use statements
        if (false !== strpos($file, DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Unused use statements must be removed.';
    }
}
