<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 22.11.17
 */

namespace GepurIt\ReportBundle\ConsoleCommand;

use Doctrine\ODM\MongoDB\DocumentManager;
use GepurIt\ReportBundle\CreateCommand\CreateReportCommandInterface;
use GepurIt\ReportBundle\ReportCommandHandler\ReportCommandHandler;
use GepurIt\SingleInstanceCommandBundle\Contract\SingleInstanceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExecuteListenReportQueueCommand
 * @package ReportBundle\Command
 */
class ExecuteReportGeneratorCommand extends Command implements SingleInstanceInterface
{
    private ReportCommandHandler $commandHandler;
    private DocumentManager $documentManager;
    private LoggerInterface $logger;
    private InputInterface $input;
    private OutputInterface $output;

    /**
     * ListenReportQueueCommand constructor.
     * @param ReportCommandHandler $commandHandler
     * @param DocumentManager $documentManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        ReportCommandHandler $commandHandler,
        DocumentManager $documentManager,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->commandHandler = $commandHandler;
        $this->documentManager = $documentManager;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('reports:generator:execute')
            ->setDescription('Execute the Queue "report_commands" from Rabbit with command messages')
            ->addArgument('command_class', InputArgument::REQUIRED, "Command class to execute")
            ->addArgument('command_id', InputArgument::REQUIRED, "Command id to execute");
    }

    /**
     * Listen the Queue "report_commands" (from Rabbit) with command messages and run process($createReportCommand)
     * @uses processEnvelope
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;

        $commandClass = $input->getArgument("command_class");
        $commandId = $input->getArgument("command_id");

        /** @var CreateReportCommandInterface|null $command */
        $command = $this->documentManager->getRepository($commandClass)->find($commandId);
        if (null === $command) {
            $this->logger->error("command {$commandClass} with id {$commandId} not found");
            return 1;
        }

        $this->commandHandler->process($command);
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
