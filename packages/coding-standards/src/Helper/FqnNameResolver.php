<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\Helper;

use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

final class FqnNameResolver
{
    /**
     * @var \PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer
     */
    private $namespaceUsesAnalyzer;

    /**
     * @param \PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer $namespaceUsesAnalyzer
     */
    public function __construct(NamespaceUsesAnalyzer $namespaceUsesAnalyzer)
    {
        $this->namespaceUsesAnalyzer = $namespaceUsesAnalyzer;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param string $className
     * @return string
     */
    public function resolve(Tokens $tokens, string $className): string
    {
        if ($className === '') {
            return '';
        }

        // probably not a class name, skip
        if (ctype_lower($className[0])) {
            return $className;
        }

        $matchedClassName = $this->matchUseImports($tokens, $className);
        if ($matchedClassName !== null) {
            return $matchedClassName;
        }

        if ($this->hasNamespace($tokens)) {
            return $this->getNamespaceAsString($tokens) . '\\' . $className;
        }

        // no namespace, return the class
        return $className;
    }

    /**
     * Tries to match names against use imports, e.g. "SomeClass" returns "SomeNamespace\SomeClass" for:
     *
     * use SomeNamespace\AnotherClass;
     * use SomeNamespace\SomeClass;
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param string $className
     * @return string|null
     */
    private function matchUseImports(Tokens $tokens, string $className): ?string
    {
        $namespaceUseAnalyses = $this->namespaceUsesAnalyzer->getDeclarationsFromTokens($tokens);

        foreach ($namespaceUseAnalyses as $namespaceUseAnalysis) {
            if ($className === $namespaceUseAnalysis->getShortName()) {
                return $namespaceUseAnalysis->getFullName();
            }
        }

        return null;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    private function hasNamespace(Tokens $tokens): bool
    {
        return (bool)$tokens->findGivenKind([T_NAMESPACE], 0);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return string
     */
    private function getNamespaceAsString(Tokens $tokens): string
    {
        $namespaceTokens = $tokens->findGivenKind([T_NAMESPACE], 0);
        $namespaceToken = array_pop($namespaceTokens);
        reset($namespaceToken);

        $namespacePosition = (int)key($namespaceToken);
        $namespaceName = '';
        $position = $namespacePosition + 2;

        while ($tokens[$position]->isGivenKind([T_NS_SEPARATOR, T_STRING])) {
            $namespaceName .= $tokens[$position]->getContent();
            ++$position;
        }

        return $namespaceName;
    }
}
