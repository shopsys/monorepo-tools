<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\CsFixer\Phpdoc;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Shopsys\CodingStandards\Helper\PhpdocRegex;

final class MissingParamAnnotationsFixer extends AbstractMissingAnnotationsFixer
{
    /**
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Methods and functions have to have @param annotation for all params',
            [new CodeSample('function someFunction(int $value) {}')]
        );
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @param \PhpCsFixer\Tokenizer\Token|null $docToken
     */
    protected function processFunctionToken(Tokens $tokens, int $index, ?Token $docToken): void
    {
        $argumentAnalyses = $this->functionsAnalyzer->getFunctionArguments($tokens, $index);
        if (count($argumentAnalyses) === 0) {
            return;
        }

        if ($docToken !== null) {
            $argumentAnalyses = $this->filterArgumentAnalysesFromExistingParamAnnotations($argumentAnalyses, $docToken);
        }

        // all arguments have annotations → skip
        if (count($argumentAnalyses) === 0) {
            return;
        }

        $indent = $this->resolveIndent($tokens, $index);

        $newLines = $this->createParamLinesFromArgumentAnalyses($tokens, $argumentAnalyses, $indent);

        if ($docToken !== null) {
            $this->updateDocWithLines($tokens, $index, $docToken, $newLines);
            return;
        }

        $this->addDocWithLines($tokens, $index, $newLines, $indent);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis[] $argumentAnalyses
     * @param \PhpCsFixer\Tokenizer\Token $docToken
     * @return array
     */
    private function filterArgumentAnalysesFromExistingParamAnnotations(array $argumentAnalyses, Token $docToken): array
    {
        $doc = new DocBlock($docToken->getContent());

        foreach ($doc->getAnnotationsOfType('param') as $annotation) {
            $matches = Strings::match($annotation->getContent(), PhpdocRegex::ARGUMENT_NAME_PATTERN);
            if (isset($matches[1])) {
                unset($argumentAnalyses[$matches[1]]);
            }
        }

        return $argumentAnalyses;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param \PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis $argumentAnalyses
     * @param string $indent
     * @return \PhpCsFixer\DocBlock\Line[]
     */
    private function createParamLinesFromArgumentAnalyses(Tokens $tokens, array $argumentAnalyses, string $indent): array
    {
        $lines = [];

        foreach ($argumentAnalyses as $argumentAnalysis) {
            $type = $this->phpToDocTypeTransformer->transform($tokens, $argumentAnalysis->getTypeAnalysis(), $argumentAnalysis->getDefault());

            $lines[] = new Line(sprintf(
                '%s * @param %s %s%s',
                $indent,
                $type ?: 'mixed',
                $argumentAnalysis->getName(),
                $this->whitespacesFixerConfig->getLineEnding()
            ));
        }

        return $lines;
    }
}
