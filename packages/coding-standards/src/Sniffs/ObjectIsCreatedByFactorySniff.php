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
        // get full name of the file class
        $fileClassName = ClassHelper::getFullyQualifiedName($file, $file->findNext(T_CLASS, 2));

        // get full name of the class that is instantiated inside of some method of the file class
        $className = '\\' . $this->naming->getClassName($file, $file->findNext(T_STRING, $position));
        $factoryName = $className . 'Factory';

        // if instantiated class is instantiated inside it's factory
        // if instantiated class doesn't have factory in the same namespace path then code is valid
        if ($factoryName === $fileClassName || !class_exists($factoryName)) {
            return;
        }

        $file->addError(
            sprintf('For creation of "%s" class use its factory "%s"', $className, $factoryName),
            $position,
            self::class
        );
    }
}
