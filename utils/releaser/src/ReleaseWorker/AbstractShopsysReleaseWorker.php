<?php

declare(strict_types=1);

namespace Shopsys\Releaser\ReleaseWorker;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\StageAwareInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

abstract class AbstractShopsysReleaseWorker implements ReleaseWorkerInterface, StageAwareInterface
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $symfonyStyle;

    /**
     * @var \Symplify\MonorepoBuilder\Release\Process\ProcessRunner
     */
    protected $processRunner;

    /**
     * @required
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyle
     * @param \Symplify\MonorepoBuilder\Release\Process\ProcessRunner $processRunner
     */
    public function autowire(SymfonyStyle $symfonyStyle, ProcessRunner $processRunner): void
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->processRunner = $processRunner;
    }

    /**
     * Check if there are some changes and if so, add them and commit them
     * @param string $message
     */
    protected function commit(string $message): void
    {
        if ($this->hasChangesToCommit() === false) {
            return;
        }

        $this->processRunner->run('git add .');
        $this->processRunner->run('git commit -m ' . $message);
    }

    /**
     * @return bool
     */
    private function hasChangesToCommit(): bool
    {
        $process = new Process(['git', 'status', '-s']);
        $process->run();

        $output = $process->getOutput();

        return (bool)empty($output);
    }
}
