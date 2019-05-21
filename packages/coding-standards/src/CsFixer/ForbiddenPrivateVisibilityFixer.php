<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\CsFixer;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Shopsys\CodingStandards\Exception\NamespaceNotFoundException;
use SplFileInfo;

final class ForbiddenPrivateVisibilityFixer implements DefinedFixerInterface, ConfigurableFixerInterface
{
    private const OPTION_ANALYZED_NAMESPACE = 'analyzed_namespaces';

    private $analyzedNamespaces = [];

    /**
     * {@inheritdoc}
     */
    public function configure(?array $configuration = null): void
    {
        if ($configuration !== null) {
            $this->analyzedNamespaces = $this->extractNamespaces($configuration);
        }
    }

    /**
     * @param array $configuration
     * @return array
     */
    private function extractNamespaces(array $configuration): array
    {
        if (!array_key_exists(self::OPTION_ANALYZED_NAMESPACE, $configuration)) {
            return [];
        }

        if (!is_array($configuration[self::OPTION_ANALYZED_NAMESPACE])) {
            throw new InvalidFixerConfigurationException($this->getName(), 'Namespace configuration has to be an array');
        }

        return $configuration[self::OPTION_ANALYZED_NAMESPACE];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Properties and methods should be public or protected in defined namespace',
            [
                new CodeSample(
                    '<?php
namespace Some\Namespace;
class SomeClass
{
private $property;
}'
                ),
                new CodeSample(
                    '<?php
namespace Some\Namespace;
class SomeClass
{
private function method()
{
    ...
}
}'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAllTokenKindsFound([T_PRIVATE]) && $this->checkNamespace($tokens);
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    private function checkNamespace(Tokens $tokens): bool
    {
        try {
            $namespace = $this->getNamespace($tokens);
        } catch (NamespaceNotFoundException $e) {
            return false;
        }

        foreach ($this->analyzedNamespaces as $analyzedNamespace) {
            if ($this->namespaceStartsWith($namespace, $analyzedNamespace)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @throws \Shopsys\CodingStandards\Exception\NamespaceNotFoundException
     * @return string
     */
    private function getNamespace(Tokens $tokens): string
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_NAMESPACE)) {
                continue;
            }

            $namespaceStartIndex = $tokens->getNextMeaningfulToken($index);
            $namespaceEndIndex = $tokens->getPrevMeaningfulToken($tokens->getNextTokenOfKind($index, [';']));

            return $tokens->generatePartialCode($namespaceStartIndex, $namespaceEndIndex);
        }

        throw new NamespaceNotFoundException('No namespace found');
    }

    /**
     * @param string $fullNamespace
     * @param string $namespacePrefix
     * @return bool
     */
    private function namespaceStartsWith(string $fullNamespace, string $namespacePrefix): bool
    {
        return strncmp($fullNamespace, $namespacePrefix, strlen($namespacePrefix)) === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens->findGivenKind(T_PRIVATE) as $index => $privateToken) {
            $tokens[$index] = new Token([T_PROTECTED, 'protected']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Shopsys/forbidden_private_visibility';
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
        return true;
    }
}
