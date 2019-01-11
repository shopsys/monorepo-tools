<?php

declare(strict_types=1);

namespace Shopsys\Releaser;

final class Stage
{
    /**
     * @var string
     */
    public const RELEASE_CANDIDATE = 'release-candidate';

    /**
     * @var string
     */
    public const RELEASE = 'release';

    /**
     * @var string
     */
    public const AFTER_RELEASE = 'after-release';
}
