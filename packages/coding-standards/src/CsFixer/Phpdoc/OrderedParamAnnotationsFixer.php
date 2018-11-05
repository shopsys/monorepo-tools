<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\CsFixer\Phpdoc;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Shopsys\CodingStandards\Helper\PhpdocRegex;
use SplFileInfo;

final class OrderedParamAnnotationsFixer implements FixerInterface, DefinedFixerInterface
{
    /**
     * @var \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer
     */
    private $functionsAnalyzer;

    /**
     * @param \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer $functionsAnalyzer
     */
    public function __construct(FunctionsAnalyzer $functionsAnalyzer)
    {
        $this->functionsAnalyzer = $functionsAnalyzer;
    }

    /**
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Sort @param annotations according to param order',
            [new CodeSample(
                <<<'SAMPLE'
/**
 * @param int $value2
 * @param int $value
 */
function someFunction($value, $value2)
{
}
SAMPLE
            )]
        );
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    /**
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $limit = $tokens->count() - 1;

        for ($index = $limit; $index > 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(T_DOC_COMMENT)) {
                continue;
            }

            if ($this->shouldSkipDocToken($token)) {
                continue;
            }

            $functionTokenPosition = $tokens->getNextTokenOfKind($index, [new Token([T_FUNCTION, 'function'])]);
            if ($functionTokenPosition === null) {
                continue;
            }

            $argumentAnalyses = $this->functionsAnalyzer->getFunctionArguments($tokens, $functionTokenPosition);
            if (count($argumentAnalyses) === 0) {
                continue;
            }

            $lines = (new DocBlock($token->getContent()))->getLines();
            $sortedLines = $this->sortParamLinesByArgumentOrder($lines, $argumentAnalyses);

            if ($lines === $sortedLines) {
                continue;
            }

            $tokens[$index] = new Token([T_DOC_COMMENT, implode('', $sortedLines)]);
        }
    }

    /**
     * @return bool
     */
    public function isRisky(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::class;
    }

    /**
     * Needs to run after @see MissingParamAnnotationsFixer
     * @return int
     */
    public function getPriority(): int
    {
        return -5;
    }

    /**
     * @param \SplFileInfo $file
     * @return bool
     */
    public function supports(SplFileInfo $file): bool
    {
        return (bool)Strings::match($file->getFilename(), '#\.php$#ui');
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Token $docToken
     * @return bool
     */
    private function shouldSkipDocToken(Token $docToken): bool
    {
        if (stripos($docToken->getContent(), 'inheritdoc') !== false) {
            return true;
        }

        $paramAnnotations = Strings::matchAll($docToken->getContent(), '#@param#m');
        if (count($paramAnnotations) < 2) {
            return true;
        }

        // ignore one-line phpdocs like `/** foo */`, as there is no place to put new annotations
        return !strpos($docToken->getContent(), "\n");
    }

    /**
     * @param \PhpCsFixer\DocBlock\Line[] $lines
     * @param \PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis[] $argumentAnalyses
     * @return \PhpCsFixer\DocBlock\Line[]
     */
    private function sortParamLinesByArgumentOrder(array $lines, array $argumentAnalyses): array
    {
        $docParamNamesToKeys = [];

        foreach ($lines as $key => $line) {
            $paramName = $this->getParamNameFromLine($line);
            if ($paramName !== null && isset($argumentAnalyses[$paramName])) {
                // use argument name as key, for sorting
                $docParamNamesToKeys[$paramName] = $key;
            }
        }

        if (count($docParamNamesToKeys) === 0) {
            return $lines;
        }

        $argumentNamesToKeys = $this->resolveArgumentNamesToKeys($argumentAnalyses, $docParamNamesToKeys);
        $properParamOrder = array_merge($docParamNamesToKeys, $argumentNamesToKeys);

        $newLines = [];
        foreach ($lines as $position => $line) {
            $paramName = $this->getParamNameFromLine($line);
            if ($paramName !== null && isset($properParamOrder[$paramName])) {
                $newPosition = $properParamOrder[$paramName];
                $newLines[$newPosition] = $line;
            } else {
                $newLines[$position] = $line;
            }
        }

        ksort($newLines);

        return $newLines;
    }

    /**
     * @param \PhpCsFixer\DocBlock\Line $line
     * @return string|null
     */
    private function getParamNameFromLine(Line $line): ?string
    {
        $matches = Strings::match($line->getContent(), PhpdocRegex::ARGUMENT_NAME_PATTERN);
        if (isset($matches[1]) && !empty($matches[1])) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis[] $argumentAnalyses
     * @param int[] $docParamNamesToKeys
     * @return int[]
     */
    private function resolveArgumentNamesToKeys(array $argumentAnalyses, array $docParamNamesToKeys): array
    {
        $paramStartingKey = min($docParamNamesToKeys);

        $argumentNamesToKeys = [];
        $i = 0;

        foreach ($argumentAnalyses as $argumentAnalysis) {
            $argumentNamesToKeys[$argumentAnalysis->getName()] = $paramStartingKey + $i;
            ++$i;
        }

        return $argumentNamesToKeys;
    }
}
