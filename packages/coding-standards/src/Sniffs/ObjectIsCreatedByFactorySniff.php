<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ClassHelper;
use Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming;

final class ObjectIsCreatedByFactorySniff implements Sniff
{
    /**
     * @var \Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming
     */
    private $naming;

    /**
     * @param \Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming $naming
     */
    public function __construct(Naming $naming)
    {
        $this->naming = $naming;
    }

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_NEW];
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $endPosition = $file->findEndOfStatement($position);
        $instantiatedClassNamePosition = $file->findNext(T_STRING, $position, $endPosition);

        if ($instantiatedClassNamePosition === false) {
            // eg. new $className; cannot be resolved
            return;
        }

        $instantiatedClassName = $this->naming->getClassName($file, $instantiatedClassNamePosition);
        $factoryClassName = $instantiatedClassName . 'Factory';
        $currentClassName = $this->getFirstClassNameInFile($file);

        if (!class_exists($factoryClassName) || is_a($currentClassName, $factoryClassName, true)) {
            return;
        }

        $file->addError(
            sprintf('For creation of "%s" class use its factory "%s"', $instantiatedClassName, $factoryClassName),
            $position,
            self::class
        );
    }

    /**
     * We can not use Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming::getClassName()
     * as it does not include namespace of declared class.
     *
     * @param \PHP_CodeSniffer\Files\File $file
     * @return string|null
     */
    private function getFirstClassNameInFile(File $file): ?string
    {
        $position = $file->findNext(T_CLASS, 0);

        if ($position === false) {
            return null;
        }

        $fileClassName = ClassHelper::getFullyQualifiedName($file, $position);

        return ltrim($fileClassName, '\\');
    }
}
