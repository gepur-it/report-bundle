<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 27.12.17
 * Time: 11:02
 */

namespace GepurIt\ReportBundle\ConsoleCommand;

use AMQPEnvelope;
use AMQPQueue;
use GepurIt\ReportBundle\CreateCommand\CreateCommandMessage;
use GepurIt\ReportBundle\Helpers\RabbitHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class RunListenReportQueueCommand
 * @package ReportBundle\Command
 * @codeCoverageIgnore
 */
class RunReportGeneratorCommand extends Command
{
    /** @var LoggerInterface */
    private $logger;
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;
    /** @var string */
    private $appDir;
    /** @var RabbitHelper */
    private $rabbit;

    /**
     * TransmitMessagesCommand constructor.
     * @param LoggerInterface $logger
     * @param \GepurIt\ReportBundle\Helpers\RabbitHelper $rabbit
     * @param string $appDir
     */
    public function __construct(LoggerInterface $logger, RabbitHelper $rabbit, string $appDir)
    {
        parent::__construct();

        $this->rabbit = $rabbit;
        $this->appDir = $appDir;
        $this->logger = $logger;
    }

    /**
     * @param $type
     * @param $buffer
     * @used-by execute
     */
    public function displayOutput($type, $buffer)
    {
        if (Process::ERR === $type) {
            $this->logger->error($buffer);
        }
        $this->output->write("$buffer");
    }

    /**
     * @used-by execute
     * @param AMQPEnvelope $envelope
     * @param AMQPQueue $queue
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     */
    public function processEnvelope(AMQPEnvelope $envelope, AMQPQueue $queue)
    {
        /** @var string $bodyString */
        $bodyString = $envelope->getBody();
        /** @var CreateCommandMessage $commandMessage */
        $commandMessage = new CreateCommandMessage(\json_decode($bodyString));
        $php = "/usr/bin/php";
        $console = $this->appDir."/bin/console";
        $command = "reports:generator:execute";
        $now = new \DateTime();
        $this->output->writeln($now->format("Y-m-d H:i:s")." commandId".$commandMessage->commandId);
        $process = new Process(
            [
                $php,
                $console,
                $command,
                $commandMessage->commandClass,
                $commandMessage->commandId,
            ]
        );
        $process->run([$this, 'displayOutput']);
        if ($process->getExitCode() === 0) {
            $queue->ack($envelope->getDeliveryTag());
        } else {
            $queue->nack($envelope->getDeliveryTag());
        }
    }

    protected function configure()
    {
        $this
            ->setName('reports:generator:run')
            ->setDescription('Start the Queue "report_commands" from Rabbit with command messages.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     * @uses displayOutput
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $queue = $this->rabbit->getQueue();

        $queue->consume([$this, 'processEnvelope']);
    }
}
