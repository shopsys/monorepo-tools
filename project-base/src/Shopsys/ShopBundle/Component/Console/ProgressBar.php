<?php

namespace Shopsys\ShopBundle\Component\Console;

use Symfony\Component\Console\Helper\ProgressBar as BaseProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ProgressBar with advanced placeholders for displaying speed etc.
 */
class ProgressBar extends BaseProgressBar {

    /**
     * @var float
     */
    private $microtimeAtLastDisplay;

    /**
     * @var int
     */
    private $progressAtLastDisplay;

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param int $max
     */
    public function __construct(OutputInterface $output, $max = 0) {
        parent::__construct($output, $max);

        $this->microtimeAtLastDisplay = microtime(true);
        $this->progressAtLastDisplay = 0;

        $this->initializeCustomPlaceholderFormatters();
    }

    private function initializeCustomPlaceholderFormatters() {
        $this->setPlaceholderFormatterDefinition('speed', function () {
            $microtimeSinceLastDisplay = microtime(true) - $this->microtimeAtLastDisplay;
            $progressSinceLastDisplay = $this->getProgress() - $this->progressAtLastDisplay;

            if ($microtimeSinceLastDisplay === 0) {
                return 0;
            }

            return $progressSinceLastDisplay / $microtimeSinceLastDisplay;
        });

        $this->setPlaceholderFormatterDefinition('step_duration', function () {
            $microtimeSinceLastDisplay = microtime(true) - $this->microtimeAtLastDisplay;
            $progressSinceLastDisplay = $this->getProgress() - $this->progressAtLastDisplay;

            if ($progressSinceLastDisplay === 0) {
                return 0;
            }

            return $microtimeSinceLastDisplay / $progressSinceLastDisplay;
        });

        $this->setPlaceholderFormatterDefinition('remaining_hms', function () {
            if (!$this->getMaxSteps()) {
                throw new \LogicException('Unable to display the remaining time if the maximum number of steps is not set.');
            }

            if ($this->getProgress() !== 0) {
                $secondsPerStep = (time() - $this->getStartTime()) / $this->getProgress();
                $remainingSteps = $this->getMaxSteps() - $this->getProgress();

                $remainingSeconds = round($secondsPerStep * $remainingSteps);
            } else {
                $remainingSeconds = 0;
            }

            return $this->formatTimeHms($remainingSeconds);
        });

        $this->setPlaceholderFormatterDefinition('elapsed_hms', function () {
            return $this->formatTimeHms(time() - $this->getStartTime());
        });
    }

    public function display() {
        parent::display();

        $this->microtimeAtLastDisplay = microtime(true);
        $this->progressAtLastDisplay = $this->getProgress();
    }

    /**
     * @param int $timeInSeconds
     * @return string
     */
    private function formatTimeHms($timeInSeconds) {
        return sprintf(
            '%dh %02dm %02ds',
            floor($timeInSeconds / 3600),
            floor(($timeInSeconds / 60) % 60),
            floor($timeInSeconds % 60)
        );
    }

}
