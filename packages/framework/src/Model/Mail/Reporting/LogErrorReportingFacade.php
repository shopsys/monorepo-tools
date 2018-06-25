<?php

namespace Shopsys\FrameworkBundle\Model\Mail\Reporting;

use DateTime;

class LogErrorReportingFacade
{
    const MAX_FILE_TAIL_LENGTH = 8000;

    /**
     * @var string
     */
    protected $logsDir;

    /**
     * @param string $logsDir
     */
    public function __construct($logsDir)
    {
        $this->logsDir = $logsDir;
    }

    /**
     * Works only for DateTime in today and yesterday!
     * @param \DateTime $from
     * @param string $rotatedLogName
     * @return bool
     */
    public function existsLogEntryFromDateTime(DateTime $from, $rotatedLogName)
    {
        $logFilepath = $this->getLogFilepathByDate($from, $rotatedLogName);
        if (file_exists($logFilepath) && filemtime($logFilepath) >= $from->getTimestamp()) {
            return true;
        }

        // if date "from" is yesterday then must check today log file
        $nextDayDate = clone $from;
        $nextDayDate->modify('+1 day');
        $nextDayLogFilepath = $this->getLogFilepathByDate($nextDayDate, $rotatedLogName);
        if (file_exists($nextDayLogFilepath)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $rotatedLogName
     * @return bool|string
     */
    public function getLogsTail($rotatedLogName)
    {
        $today = new DateTime();
        $todayLogFilepath = $this->getLogFilepathByDate($today, $rotatedLogName);
        $logTail = $this->getLogFileTail($todayLogFilepath);

        if ($logTail === false || $logTail === '') {
            $yesterday = new DateTime('yesterday');
            $yesterdayLogFilepath = $this->getLogFilepathByDate($yesterday, $rotatedLogName);
            $logTail = $this->getLogFileTail($yesterdayLogFilepath);
        }

        return $logTail;
    }

    /**
     * @param string $filepath
     * @return bool|string
     */
    protected function getLogFileTail($filepath)
    {
        if (!file_exists($filepath)) {
            return false;
        }

        $file = fopen($filepath, 'rb');
        fseek($file, -self::MAX_FILE_TAIL_LENGTH, SEEK_END);
        $fileTail = fread($file, self::MAX_FILE_TAIL_LENGTH);
        if (!is_string($fileTail)) {
            return false;
        }

        if (filesize($filepath) > self::MAX_FILE_TAIL_LENGTH) {
            $fileTail = '...' . $fileTail;
        }

        return $fileTail;
    }

    /**
     * @param \DateTime $date
     * @param string $rotatedLogName
     * @return string
     */
    protected function getLogFilepathByDate(DateTime $date, $rotatedLogName)
    {
        return $this->logsDir . '/'
            . $rotatedLogName
            . '-'
            . $date->format('Y-m-d')
            . '.log';
    }
}
