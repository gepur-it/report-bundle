<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 20.11.17
 */

namespace GepurIt\ReportBundle\Document;

use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use GepurIt\ReportBundle\CreateCommand\CreateReportCommandInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Class AbstractCreateReportCommand
 * @package ReportBundle\CreateCommand
 * @MongoDB\MappedSuperclass()
 * @MongoDB\InheritanceType("COLLECTION_PER_CLASS")
 * @MongoDB\HasLifecycleCallbacks()
 * @codeCoverageIgnore
 */
abstract class AbstractCreateReportCommand implements CreateReportCommandInterface
{
    /**
     * @MongoDB\Id(strategy="NONE", type="string")
     * @var string
     */
    protected string $commandId = '';

    /**
     * @var DateTime|null
     * @MongoDB\Field(type="date")
     * @MongoDB\Index(order="desc")
     */
    protected ?DateTime $createdAt = null;

    /**
     * @var int
     * @MongoDB\Field(type="int")
     */
    protected int $status = CreateReportCommandInterface::STATUS__NEW;

    /**
     * @var array
     * @MongoDB\Field(type="collection")
     */
    protected iterable $errors = [];


    /**
     * AbstractCreateReportCommand constructor.
     */
    public function __construct()
    {
        $this->commandId = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTime();
    }

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
    public function setCommandId(string $commandId)
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
