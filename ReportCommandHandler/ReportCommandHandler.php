<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 20.11.17
 */

namespace GepurIt\ReportBundle\ReportCommandHandler;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use GepurIt\ReportBundle\CreateCommand\CreateCommandMessage;
use GepurIt\ReportBundle\CreateCommand\CreateReportCommandInterface;
use GepurIt\ReportBundle\Exception\GeneratorNotFoundException;
use GepurIt\ReportBundle\Helpers\RabbitHelper;
use GepurIt\ReportBundle\ReportGenerator\ReportGeneratorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Yawa20\RegistryBundle\Registry\SimpleRegistry;

/**
 * Class ReportCommandHandler
 * @package ReportBundle\ReportCommandHandler
 * @method ReportGeneratorInterface get(string $key)
 */
class ReportCommandHandler extends SimpleRegistry
{
    /** @var DocumentManager */
    private $documentManager;
    /** @var LoggerInterface */
    private $logger;
    /** @var RabbitHelper */
    private $rabbit;

    /**
     * ReportCommandHandler constructor.
     * @param DocumentManager $documentManager
     * @param LoggerInterface $logger
     * @param RabbitHelper $rabbit
     */
    public function __construct(
        DocumentManager $documentManager,
        LoggerInterface $logger,
        RabbitHelper $rabbit
    ) {
        $this->documentManager = $documentManager;
        $this->logger = $logger;
        $this->rabbit = $rabbit;

        parent::__construct(ReportGeneratorInterface::class);
    }

    /**
     * Push the Command to MongoDB && Rabbit (in queue)
     * @param CreateReportCommandInterface $createReportCommand
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     * @throws MongoDBException
     */
    public function push(CreateReportCommandInterface $createReportCommand)
    {
        $this->documentManager->persist($createReportCommand);
        if (empty($createReportCommand->getCommandId())) {
            $createReportCommand->setCommandId(Uuid::v4()->toRfc4122());
        }
        $this->documentManager->flush();
        $this->addToRabbitQueue($createReportCommand);
    }

    /**
     * Execute command $createReportCommand which was listened from Queue
     * @param CreateReportCommandInterface $createReportCommand
     * @return bool
     * @throws \Exception
     */
    public function process(CreateReportCommandInterface $createReportCommand)
    {
        $key = get_class($createReportCommand);
        if (!$this->exists($key)) {
            throw new GeneratorNotFoundException($key);
        }
        $createReportCommand->setStatus(CreateReportCommandInterface::STATUS__IN_PROGRESS);
        $this->documentManager->persist($createReportCommand);
        $this->documentManager->flush();
        $errors = $this->get($key)->generate($createReportCommand);
        if (count($errors) > 0) {
            $createReportCommand->setStatus(CreateReportCommandInterface::STATUS__ERROR);
            foreach ($errors as $error) {
                $createReportCommand->addError($error);
            }
            $this->documentManager->persist($createReportCommand);
            $this->documentManager->flush();

            return false;
        }
        $createReportCommand->setStatus(CreateReportCommandInterface::STATUS__FINISHED);
        $this->documentManager->persist($createReportCommand);
        $this->documentManager->flush();

        return true;
    }

    /**
     * @param $createReportCommand CreateReportCommandInterface
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    private function addToRabbitQueue(CreateReportCommandInterface $createReportCommand)
    {
        $commandName = get_class($createReportCommand);
        $commandId = $createReportCommand->getCommandId();
        $message = new CreateCommandMessage();
        $message->commandClass = $commandName;
        $message->commandId = $commandId;

        $exchange = $this->rabbit->getExchange();
        $exchange->publish(json_encode($message), RabbitHelper::QUEUE__NAME);
    }
}
