<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 01.12.17
 */

namespace GepurIt\ReportBundle\Tests;

use Doctrine\ODM\MongoDB\DocumentManager;
//use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use GepurIt\ReportBundle\ConsoleCommand\ExecuteReportGeneratorCommand;
use GepurIt\ReportBundle\CreateCommand\CreateReportCommandInterface;
use GepurIt\ReportBundle\Exception\GeneratorNotFoundException;
use GepurIt\ReportBundle\ReportCommandHandler\ReportCommandHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExecuteReportGeneratorCommandTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testProcessEnvelopeSuccess()
    {
        $commandId = 'some_id';
        $commandClass = 'some_class';

        $inputInterface = $this->createMock(InputInterface::class);
        $inputInterface->expects($this->exactly(2))
            ->method('getArgument')
            ->willReturnCallback(
                function ($argument) use ($commandClass, $commandId) {
                    $arr = [
                        'command_class' => $commandClass,
                        'command_id'    => $commandId,
                    ];

                    return $arr[$argument];
                }
            );
        /** @var CreateReportCommandInterface|MockObject $commandMock */
        $commandMock = $this->createMock(CreateReportCommandInterface::class);

        /** @var ReportCommandHandler|MockObject $handlerMock */
        $handlerMock = $this->createMock(ReportCommandHandler::class);
        $handlerMock
            ->expects($this->once())
            ->method('process')
            ->with($commandMock)
            ->willReturn(true);

        /** @var DocumentRepository|MockObject $documentRepository */
        $documentRepository = $this->createMock(DocumentRepository::class);
        $documentRepository
            ->expects($this->once())
            ->method('find')
            ->with($commandId)
            ->willReturn($commandMock);

        /** @var DocumentManager|MockObject $documentManager */
        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($commandClass)
            ->willReturn($documentRepository);
        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);
        $listener = new ExecuteReportGeneratorCommand($handlerMock, $documentManager, $loggerMock);

        $outputInterface = $this->createMock(OutputInterface::class);
        $listener->run($inputInterface, $outputInterface);
    }

    /**
     * @throws \Exception
     */
    public function testProcessEnvelopeCommandNull()
    {
        $commandId = 'some_id';
        $commandClass = 'some_class';
        $inputInterface = $this->createMock(InputInterface::class);
        $inputInterface->expects($this->exactly(2))
            ->method('getArgument')
            ->willReturnCallback(
                function ($argument) use ($commandClass, $commandId) {
                    $arr = [
                        'command_class' => $commandClass,
                        'command_id'    => $commandId,
                    ];

                    return $arr[$argument];
                }
            );

        /** @var ReportCommandHandler|MockObject $handlerMock */
        $handlerMock = $this->createMock(ReportCommandHandler::class);
        /** @var DocumentRepository|MockObject $documentRepository */
        $documentRepository = $this->createMock(DocumentRepository::class);
        $documentRepository
            ->expects($this->once())
            ->method('find')
            ->with($commandId)
            ->willReturn(null);

        /** @var DocumentManager|MockObject $documentManager */
        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($commandClass)
            ->willReturn($documentRepository);

        $message = 'command '.$commandClass.' with id '.$commandId.' not found';
        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock
            ->expects($this->once())
            ->method('error')
            ->with($message)
            ->willReturn(null);
        $listener = new ExecuteReportGeneratorCommand($handlerMock, $documentManager, $loggerMock);
        $outputInterface = $this->createMock(OutputInterface::class);
        $listener->run($inputInterface, $outputInterface);
    }

    /**
     * @throws \Exception
     */
    public function testProcessEnvelopeError()
    {
        $commandId = 'some_id';
        $commandClass = 'some_class';
        $inputInterface = $this->createMock(InputInterface::class);
        $inputInterface->expects($this->exactly(2))
            ->method('getArgument')
            ->willReturnCallback(
                function ($argument) use ($commandClass, $commandId) {
                    $arr = [
                        'command_class' => $commandClass,
                        'command_id'    => $commandId,
                    ];

                    return $arr[$argument];
                }
            );

        /** @var GeneratorNotFoundException|MockObject $exception */
        $exception = $this->createMock(GeneratorNotFoundException::class);

        /** @var CreateReportCommandInterface|MockObject $commandMock */
        $commandMock = $this->createMock(CreateReportCommandInterface::class);
        $commandMock
            ->expects($this->never())
            ->method('setStatus');
        $commandMock
            ->expects($this->never())
            ->method('addError');

        /** @var ReportCommandHandler|MockObject $handlerMock */
        $handlerMock = $this->createMock(ReportCommandHandler::class);
        $handlerMock
            ->expects($this->once())
            ->method('process')
            ->with($commandMock)
            ->willThrowException($exception);

        /** @var DocumentRepository|MockObject $documentRepository */
        $documentRepository = $this->createMock(DocumentRepository::class);
        $documentRepository
            ->expects($this->once())
            ->method('find')
            ->with($commandId)
            ->willReturn($commandMock);

        /** @var DocumentManager|MockObject $documentManager */
        $documentManager = $this->createMock(DocumentManager::class);
        $documentManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($commandClass)
            ->willReturn($documentRepository);
        $documentManager
            ->expects($this->never())
            ->method('persist');
        $documentManager
            ->expects($this->never())
            ->method('flush');

        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        $listener = new ExecuteReportGeneratorCommand($handlerMock, $documentManager, $loggerMock);
        $outputInterface = $this->createMock(OutputInterface::class);
        $this->expectException(GeneratorNotFoundException::class);
        $listener->run($inputInterface, $outputInterface);
    }
}
