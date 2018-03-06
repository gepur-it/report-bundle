<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 20.11.17
 */

namespace GepurIt\ReportBundle\CreateCommand;

use DateTime;

/**
 * Interface CreateReportCommandInterface
 * @package ReportBundle\Command
 */
interface CreateReportCommandInterface
{
    const STATUS__NEW = 0;
    const STATUS__IN_PROGRESS = 1;
    const STATUS__FINISHED = 2;
    const STATUS__ERROR = 3;

    /** @return string */
    public function getCommandId(): string;

    /** @return DateTime */
    public function getCreatedAt(): DateTime;

    /** @param int $status */
    public function setStatus(int $status);

    /** @return int */
    public function getStatus(): int;

    /** @return array */
    public function getErrors(): array;

    /** @param $message string */
    public function addError($message);
}