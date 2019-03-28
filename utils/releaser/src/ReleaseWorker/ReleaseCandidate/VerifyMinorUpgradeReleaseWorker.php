<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker\ReleaseCandidate;

use PharIo\Version\Version;
use Shopsys\Releaser\ReleaseWorker\AbstractShopsysReleaseWorker;
use Shopsys\Releaser\Stage;

final class VerifyMinorUpgradeReleaseWorker extends AbstractShopsysReleaseWorker
{
    /**
     * @param \PharIo\Version\Version $version
     * @return string
     */
    public function getDescription(Version $version): string
    {
        return '[Manually] Verify there are no BC-breaks when releasing a minor version';
    }

    /**
     * Higher first
     * @return int
     */
    public function getPriority(): int
    {
        return 732;
    }

    /**
     * @param \PharIo\Version\Version $version
     */
    public function work(Version $version): void
    {
        $this->symfonyStyle->note(sprintf(
            'When releasing a minor version, you need to verify there are no BC-breaks. Suggested steps:
            - install demoshop project,
            - in demoshop composer.json, change a version of all shopsys/* packages to "dev-%s",
            - build the demoshop application using "php phing build-demo-dev, 
            - run acceptance tests using "php phing tests-acceptance"',
            $this->createBranchName($version)
        ));
        $this->confirm('Confirm the minor version does not contain any BC-breaks.');
    }

    /**
     * @return string
     */
    public function getStage(): string
    {
        return Stage::RELEASE_CANDIDATE;
    }
}
