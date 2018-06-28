<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class FileExtensionMaxLength extends Constraint
{
    /**
     * @var string
     */
    public $message = 'File extension {{ value }} is too long. It should have {{ limit }} character or less.';

    /**
     * @var int
     */
    public $limit;

    public function getRequiredOptions()
    {
        return [
            'limit',
        ];
    }

    public function getDefaultOption()
    {
        return 'limit';
    }
}
