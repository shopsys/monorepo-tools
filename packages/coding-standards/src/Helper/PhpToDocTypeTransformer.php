<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\Helper;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Tokens;

final class PhpToDocTypeTransformer
{
    /**
     * @var \Shopsys\CodingStandards\Helper\FqnNameResolver
     */
    private $fqnNameResolver;

    /**
     * @param \Shopsys\CodingStandards\Helper\FqnNameResolver $fqnNameResolver
     */
    public function __construct(FqnNameResolver $fqnNameResolver)
    {
        $this->fqnNameResolver = $fqnNameResolver;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param \PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis|null $typeAnalysis
     * @param mixed $default
     * @return string
     */
    public function transform(Tokens $tokens, ?TypeAnalysis $typeAnalysis, $default = null): string
    {
        if ($typeAnalysis === null) {
            $type = 'mixed';
        } else {
            if ($typeAnalysis->isReservedType() && $default === null) {
                return $typeAnalysis->getName();
            }

            $type = $typeAnalysis->getName();
        }

        // nullable
        if ($type[0] === '?' || (is_string($default) && strtolower($default) === 'null')) {
            return $this->createFromNullable($tokens, $type);
        }

        $type = $this->fqnNameResolver->resolve($tokens, $type);

        return $this->preSlashType($type);
    }

    /**
     * @param string $type
     * @return string
     */
    private function preSlashType(string $type): string
    {
        if (Strings::startsWith($type, '\\')) {
            return $type;
        }

        if (class_exists($type) || interface_exists($type)) {
            return '\\' . $type;
        }

        return $type;
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param string $type
     * @return string
     */
    private function createFromNullable(Tokens $tokens, string $type): string
    {
        // cleanup from "?"
        $type = $type[0] === '?' ? substr($type, 1) : $type;
        $type = $this->fqnNameResolver->resolve($tokens, $type);

        return $this->preSlashType($type) . '|null';
    }
}
