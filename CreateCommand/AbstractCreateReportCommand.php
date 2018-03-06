<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 20.11.17
 */

namespace GepurIt\ReportBundle\CreateCommand;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use DateTime;

/**
 * Class AbstractCreateReportCommand
 * @package ReportBundle\CreateCommand
 * @MongoDB\Document(
 *      repositoryClass="ReportBundle\ReportType\BaseReportCommandRepository"
 * )
 * @MongoDB\MappedSuperclass()
 * @MongoDB\InheritanceType("COLLECTION_PER_CLASS")
 * @MongoDB\HasLifecycleCallbacks()
 * @codeCoverageIgnore
 */
abstract class AbstractCreateReportCommand implements CreateReportCommandInterface
{
    /**
     * @MongoDB\Id(strategy="UUID")
     * @var string
     */
    protected $commandId;

    /**
     * @var DateTime
     * @MongoDB\Field(type="date")
     * @MongoDB\Index(order="desc")
     */
    protected $createdAt;

    /**
     * @var int
     * @MongoDB\Field(type="integer")
     */
    protected $status = CreateReportCommandInterface::STATUS__NEW;
    /**
     * @var array
     * @MongoDB\Field(type="collection")
     */
    protected $errors = [];

    /**
     * @return string
     */
    public function getCommandId(): string
    {
        return $this->commandId;
    }

    /**
     * @param string $commandId
     */
    public function setCommandId($commandId)
    {
        $this->commandId = $commandId;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @MongoDB\PrePersist()
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param $message string | array
     */
    public function addError($message)
    {
        array_push($this->errors, $message);
    }
}