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
use GepurIt\SingleInstanceCommandBundle\Contract\SingleInstanceInterface;
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
class RunReportGeneratorCommand extends Command implements SingleInstanceInterface
{
    private LoggerInterface $logger;
    private string $appDir;
    private RabbitHelper $rabbit;
    private InputInterface $input;
    private OutputInterface $output;

    /**
     * TransmitMessagesCommand constructor.
     * @param LoggerInterface $logger
     * @param RabbitHelper $rabbit
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
        $bodyString = $envelope->getBody();
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
            ], null, null, null, null
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
     * @return int
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     * @uses displayOutput
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $queue = $this->rabbit->getQueue();

        $queue->consume([$this, 'processEnvelope']);

        return 0;
    }
    
    /**
     * get`s lock name for command execution, based on input
     *
     * @param InputInterface $input
     *
     * @return string
     */
    public function getLockName(InputInterface $input): string
    {
        return $this->getName();
    }
}
