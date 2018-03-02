<?php

namespace Shopsys\FrameworkBundle\Component\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ProgressBarFactory
{
    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param int $max
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    public function create(OutputInterface $output, $max)
    {
        $bar = new ProgressBar($output, $max);
        $this->initializeCustomPlaceholderFormatters();
        $bar->setFormat(
            '%current%/%max% [%bar%] %percent:3s%, Elapsed: %elapsed_hms%, Remaining: %remaining_hms%, MEM:%memory:9s%'
        );
        $bar->setRedrawFrequency(10);
        $bar->start();

        return $bar;
    }

    private function initializeCustomPlaceholderFormatters()
    {
        ProgressBar::setPlaceholderFormatterDefinition('remaining_hms', function (ProgressBar $bar) {
            if ($bar->getProgress() !== 0) {
                $secondsPerStep = (time() - $bar->getStartTime()) / $bar->getProgress();
                $remainingSteps = $bar->getMaxSteps() - $bar->getProgress();

                $remainingSeconds = round($secondsPerStep * $remainingSteps);
            } else {
                $remainingSeconds = 0;
            }

            return $this->formatTimeHms($remainingSeconds);
        });

        ProgressBar::setPlaceholderFormatterDefinition('elapsed_hms', function (ProgressBar $bar) {
            return $this->formatTimeHms(time() - $bar->getStartTime());
        });
    }

    /**
     * @param int $timeInSeconds
     * @return string
     */
    private function formatTimeHms($timeInSeconds)
    {
        return sprintf(
            '%dh %02dm %02ds',
            floor($timeInSeconds / 3600),
            floor(($timeInSeconds / 60) % 60),
            floor($timeInSeconds % 60)
        );
    }
}
