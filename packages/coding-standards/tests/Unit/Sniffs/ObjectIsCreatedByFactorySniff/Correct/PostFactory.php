<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ObjectIsCreatedByFactorySniff\Correct;

final class PostFactory
{
    public function create()
    {
        $post = new Post();
    }
}
