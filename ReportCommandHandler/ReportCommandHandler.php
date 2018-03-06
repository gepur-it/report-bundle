<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 20.11.17
 */

namespace GepurIt\ReportBundle\ReportCommandHandler;

use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use GepurIt\ReportBundle\CreateCommand\CreateCommandMessage;
use GepurIt\ReportBundle\CreateCommand\CreateReportCommandInterface;
use GepurIt\ReportBundle\Exception\GeneratorNotFoundException;
use GepurIt\ReportBundle\ReportGenerator\ReportGeneratorInterface;

/**
 * Class ReportCommandHandler
 * @package ReportBundle\ReportCommandHandler
 */
class ReportCommandHandler
{
    /** @var ReportGeneratorInterface[] //map to associate command with concrete generator */
    private $generatorsMap = [];
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
    }

    /**
     * Push the Command to MongoDB && Rabbit (in queue)
     * @param CreateReportCommandInterface $createReportCommand
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function push(CreateReportCommandInterface $createReportCommand)
    {
        $this->documentManager->persist($createReportCommand);
        $this->documentManager->flush($createReportCommand);
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
        if (!isset($this->generatorsMap[$key])) {
            throw new GeneratorNotFoundException($key);
        }
        $createReportCommand->setStatus(CreateReportCommandInterface::STATUS__IN_PROGRESS);
        $this->documentManager->persist($createReportCommand);
        $this->documentManager->flush($createReportCommand);
        $errors = $this->generatorsMap[$key]->generate($createReportCommand);
        if (count($errors) > 0) {
            $createReportCommand->setStatus(CreateReportCommandInterface::STATUS__ERROR);
            foreach ($errors as $error) {
                $createReportCommand->addError($error);
            }
            $this->documentManager->persist($createReportCommand);
            $this->documentManager->flush($createReportCommand);

            return false;
        }
        $createReportCommand->setStatus(CreateReportCommandInterface::STATUS__FINISHED);
        $this->documentManager->persist($createReportCommand);
        $this->documentManager->flush($createReportCommand);

        return true;
    }

    /**
     * @param string $commandClass
     * @param ReportGeneratorInterface $reportGenerator
     */
    public function addGenerator(string $commandClass, ReportGeneratorInterface $reportGenerator)
    {
        $this->generatorsMap[$commandClass] = $reportGenerator;
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
