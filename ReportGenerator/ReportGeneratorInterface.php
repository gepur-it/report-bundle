<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 20.11.17
 */

namespace GepurIt\ReportBundle\ReportGenerator;

use GepurIt\ReportBundle\CreateCommand\CreateReportCommandInterface;

/**
 * Interface ReportGeneratorInterface
 * @package ReportBundle\ReportGenerator
 */
interface ReportGeneratorInterface
{
    /**
     * @param $command CreateReportCommandInterface
     * @return array Errors
     */
    public function generate(CreateReportCommandInterface $command): array;
}
