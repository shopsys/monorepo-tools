<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ObjectIsCreatedByFactorySniff\Wrong;

final class SomeController
{
    public function action()
    {
        $post = new Post();
    }
}
