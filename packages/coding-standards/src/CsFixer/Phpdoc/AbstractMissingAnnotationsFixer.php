<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\CsFixer\Phpdoc;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use Shopsys\CodingStandards\Helper\PhpToDocTypeTransformer;
use SplFileInfo;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector;

/**
 * Some code used from:
 * - @see \PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer
 * - @see \PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer
 *
 * Inspiration:
 * - https://github.com/FriendsOfPHP/PHP-CS-Fixer/commit/fbca90cc5837b26996d41f02b4ba5c759943c8fa
 */
abstract class AbstractMissingAnnotationsFixer implements FixerInterface, DefinedFixerInterface
{
    /**
     * @var \PhpCsFixer\WhitespacesFixerConfig
     */
    protected $whitespacesFixerConfig;

    /**
     * @var \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer
     */
    protected $functionsAnalyzer;

    /**
     * @var \Symplify\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector
     */
    private $indentDetector;

    /**
     * @var \Shopsys\CodingStandards\Helper\PhpToDocTypeTransformer
     */
    protected $phpToDocTypeTransformer;

    /**
     * @param \PhpCsFixer\WhitespacesFixerConfig $whitespacesFixerConfig
     * @param \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer $functionsAnalyzer
     * @param \Shopsys\CodingStandards\Helper\PhpToDocTypeTransformer $phpToDocTypeTransformer
     * @param \Symplify\TokenRunner\Analyzer\FixerAnalyzer\IndentDetector $indentDetector
     */
    public function __construct(
        WhitespacesFixerConfig $whitespacesFixerConfig,
        FunctionsAnalyzer $functionsAnalyzer,
        PhpToDocTypeTransformer $phpToDocTypeTransformer,
        IndentDetector $indentDetector
    ) {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->functionsAnalyzer = $functionsAnalyzer;
        $this->indentDetector = $indentDetector;
        $this->phpToDocTypeTransformer = $phpToDocTypeTransformer;
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
            if (!$token->isGivenKind(T_FUNCTION)) {
                continue;
            }

            if ($this->shouldSkipFunctionToken($tokens, $index)) {
                continue;
            }

            $docToken = $this->getDocToken($tokens, $index);
            if ($docToken !== null && $this->shouldSkipDocToken($docToken)) {
                continue;
            }

            $this->processFunctionToken($tokens, $index, $docToken);
        }
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @param \PhpCsFixer\Tokenizer\Token|null $docToken
     */
    abstract protected function processFunctionToken(Tokens $tokens, int $index, ?Token $docToken): void;

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_FUNCTION);
    }

    /**
     * @return bool
     */
    public function isRisky(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return static::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
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
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @return bool
     */
    protected function shouldSkipFunctionToken(Tokens $tokens, int $index): bool
    {
        $nextTokenPosition = $tokens->getNextMeaningfulToken($index);

        // anonymous functions
        return !$tokens[$nextTokenPosition]->isGivenKind(T_STRING);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Token $docToken
     * @return bool
     */
    protected function shouldSkipDocToken(Token $docToken): bool
    {
        if (stripos($docToken->getContent(), 'inheritdoc') !== false) {
            return true;
        }

        // ignore one-line phpdocs like `/** foo */`, as there is no place to put new annotations
        return strpos($docToken->getContent(), "\n") === false;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @return string
     */
    protected function resolveIndent(Tokens $tokens, int $index): string
    {
        return str_repeat($this->whitespacesFixerConfig->getIndent(), $this->indentDetector->detectOnPosition($tokens, $index));
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @return int
     */
    protected function getDocIndex(Tokens $tokens, int $index): int
    {
        do {
            $index = $tokens->getPrevNonWhitespace($index);
        } while ($tokens[$index]->isGivenKind([T_STATIC, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_ABSTRACT, T_COMMENT]));

        return $index;
    }

    /**
     * @param \PhpCsFixer\DocBlock\Line[] $newLines
     * @param string $indent
     * @return string
     */
    protected function createDocContentFromLinesAndIndent(array $newLines, string $indent): string
    {
        $lines = [];
        $lines[] = '/**' . $this->whitespacesFixerConfig->getLineEnding();
        $lines = array_merge($lines, $newLines);
        $lines[] = $indent . ' */';

        return implode('', $lines);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Token $docToken
     * @param \PhpCsFixer\DocBlock\Line[] $newLines
     * @param int|null $offset
     * @return string
     */
    protected function createDocContentFromDocTokenAndNewLines(Token $docToken, array $newLines): string
    {
        $doc = new DocBlock($docToken->getContent());
        $lines = $doc->getLines();

        array_splice(
            $lines,
            $this->resolveOffset($docToken, $newLines),
            0,
            $newLines
        );

        return implode('', $lines);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @return int
     */
    protected function getNewDocIndex(Tokens $tokens, int $index): int
    {
        for ($i = $index; $i > 0; --$i) {
            if ($this->isWhitespaceWithNewline($tokens, $i)) {
                return $i + 1;
            }
        }

        return $index;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @param \PhpCsFixer\Tokenizer\Token $docToken
     * @param \PhpCsFixer\DocBlock\Line[] $newLines
     */
    protected function updateDocWithLines(Tokens $tokens, int $index, Token $docToken, array $newLines): void
    {
        $docBlockIndex = $this->getDocIndex($tokens, $index);
        $docContent = $this->createDocContentFromDocTokenAndNewLines($docToken, $newLines);

        $tokens[$docBlockIndex] = new Token([T_DOC_COMMENT, $docContent]);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @param \PhpCsFixer\DocBlock\Line[] $newLines
     * @param string $indent
     */
    protected function addDocWithLines(Tokens $tokens, int $index, array $newLines, string $indent): void
    {
        $docBlockIndex = $this->getNewDocIndex($tokens, $index);
        $docContent = $this->createDocContentFromLinesAndIndent($newLines, $indent);

        $tokens->insertAt($docBlockIndex, new Token([T_DOC_COMMENT, $docContent]));
        $whitespaceAfterDocBlock = $this->whitespacesFixerConfig->getLineEnding() . $indent;
        $tokens->ensureWhitespaceAtIndex($docBlockIndex, 1, $whitespaceAfterDocBlock);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @return \PhpCsFixer\Tokenizer\Token|null
     */
    private function getDocToken(Tokens $tokens, int $index): ?Token
    {
        $docIndex = $this->getDocIndex($tokens, $index);
        $docToken = $tokens[$docIndex];

        if ($docToken->isGivenKind(T_DOC_COMMENT)) {
            return $docToken;
        }

        return null;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @return bool
     */
    private function isWhitespaceWithNewline(Tokens $tokens, int $index): bool
    {
        if (!$tokens[$index]->isWhitespace()) {
            return false;
        }

        $content = $tokens[$index]->getContent();

        return Strings::contains($content, $this->whitespacesFixerConfig->getLineEnding());
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Token $docToken
     * @param \PhpCsFixer\DocBlock\Line[] $newLines
     * @return int
     */
    private function resolveOffset(Token $docToken, array $newLines): int
    {
        foreach ($newLines as $newLine) {
            if (Strings::contains($newLine->getContent(), '@param') && Strings::contains($docToken->getContent(), '@param')) {
                return $this->getLastParamLinePosition($docToken);
            }
        }

        $doc = new DocBlock($docToken->getContent());
        return count($doc->getLines()) - 1;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Token $docToken
     * @return int|null
     */
    private function getLastParamLinePosition(Token $docToken): ?int
    {
        $doc = new DocBlock($docToken->getContent());

        $lastParamLine = null;
        foreach ($doc->getAnnotationsOfType('param') as $annotation) {
            $lastParamLine = max($lastParamLine, $annotation->getEnd());
        }

        return $lastParamLine;
    }
}
