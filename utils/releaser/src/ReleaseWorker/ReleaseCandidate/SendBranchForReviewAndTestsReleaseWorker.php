<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class SendBranchForReviewAndTestsReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return '[Manually] Send the branch for review and tests';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 730;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->symfonyStyle->note('Keep in mind that if there are any notes from the code review or tests, you need to address them, commit and push the fixes, force-split your branch using tool-monorepo-force-split-branch and build test-rc-project-base special job on Heimdall');
        $this->confirm('Confirm the branch is sent to code-review');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
