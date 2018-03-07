<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 20.11.17
 */

namespace GepurIt\ReportBundle\ReportGenerator;

use GepurIt\ReportBundle\CreateCommand\CreateReportCommandInterface;
use Yawa20\RegistryBundle\Registrable\RegistrableInterface;

/**
 * Interface ReportGeneratorInterface
 * @package ReportBundle\ReportGenerator
 */
interface ReportGeneratorInterface extends RegistrableInterface
{
    /**
     * @param $command CreateReportCommandInterface
     * @return array Errors
     */
    public function generate(CreateReportCommandInterface $command): array;
}
