<?php

namespace Tests\ShopBundle\Performance;

class JmeterCsvReporter
{
    /**
     * @param resource $handle
     */
    public function writeHeader($handle)
    {
        fputcsv($handle, [
            'timestamp',
            'elapsed',
            'label',
            'responseCode',
            'success',
            'URL',
            'Variables',
        ]);
    }

    /**
     * @param resource $handle
     * @param float $duration
     * @param string $routeName
     * @param int $statusCode
     * @param bool $isSuccessful
     * @param string $relativeUrl
     * @param int $queryCount
     */
    public function writeLine($handle, $duration, $routeName, $statusCode, $isSuccessful, $relativeUrl, $queryCount)
    {
        fputcsv($handle, [
            time(),
            round($duration),
            $routeName,
            $statusCode,
            $isSuccessful ? 'true' : 'false',
            '/' . $relativeUrl,
            $queryCount,
        ]);
    }
}
