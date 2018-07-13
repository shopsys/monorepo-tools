<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ObjectIsCreatedByFactorySniff\Wrong;

final class PostFactory
{
    public function create()
    {
        $post = new Post();
    }
}
