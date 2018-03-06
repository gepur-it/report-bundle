<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 31.01.18
 * Time: 16:39
 */

namespace GepurIt\ReportBundle\Helpers;

/**
 * Class FormatDateHelper
 * @codeCoverageIgnore
 */
class DateHelper
{
    /**
     * @param int $timestamp
     * @param string|null $format , by default, format "%aд. %H:%I:%S" or "%H:%I:%S" if not enough time
     * @return string
     */
    public function formatTime(int $timestamp, string $format = null): string
    {
        if (null === $format) {
            $format =  ($timestamp < 86400) ? "%H:%I:%S" : "%aд. %H:%I:%S";
        }
        if ($timestamp < 0) {
            return '';
        }
        try {
            $from = new \DateTime();
            $toDate = new \DateTime();
            $diff = new \DateInterval("PT{$timestamp}S");
            $toDate =  $toDate->add($diff);
            $returnDiff = $from->diff($toDate);
        } catch (\Exception $exception) {
            return 'ошибка формирования даты '.$exception->getMessage();
        }
        return $returnDiff->format($format);
    }
}
