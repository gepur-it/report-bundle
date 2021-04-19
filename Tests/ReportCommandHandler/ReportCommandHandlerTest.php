<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 01.12.17
 */

namespace GepurIt\ReportBundle\Tests\ReportCommandHandler;

use AMQPExchange;
use Doctrine\ODM\MongoDB\DocumentManager;
use GepurIt\ReportBundle\CreateCommand\CreateReportCommandInterface;
use GepurIt\ReportBundle\Exception\GeneratorNotFoundException;
use GepurIt\ReportBundle\Helpers\RabbitHelper;
use GepurIt\ReportBundle\ReportCommandHandler\ReportCommandHandler;
use GepurIt\ReportBundle\ReportGenerator\ReportGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class ReportCommandHandlerTest
 * @package ReportBundle\Tests
 */
class ReportCommandHandlerTest extends TestCase
{
    /**
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     * @throws \ReflectionException
     */
    public function testPush()
    {
        /** @var CreateReportCommandInterface|MockObject $commandMock */
        $commandMock = $this->createMock(CreateReportCommandInterface::class);
        $commandMock
            ->expects($this->once())
            ->method('getCommandId')
            ->willReturn('stringId');

        /** @var DocumentManager|MockObject $documentManagerMock */
        $documentManagerMock = $this->createMock(DocumentManager::class);
        $documentManagerMock
            ->expects($this->once())
            ->method('persist')
            ->with($commandMock)
            ->willReturn(null);
        $documentManagerMock
            ->expects($this->once())
            ->method('flush')
            ->with($commandMock)
            ->willReturn(null);

        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        /** @var AMQPExchange|MockObject $exchangeMock */
        $exchangeMock = $this->createMock(AMQPExchange::class);

        /** @var RabbitHelper|MockObject $rabbitMock */
        $rabbitMock = $this->createMock(RabbitHelper::class);
        $rabbitMock
            ->expects($this->once())
            ->method('getExchange')
            ->willReturn($exchangeMock);

        $handler = new ReportCommandHandler(
            $documentManagerMock,
            $loggerMock,
            $rabbitMock
        );
        $handler->push($commandMock);
    }

    /**
     * @throws \Exception
     */
    public function testProcessSuccess()
    {
        /** @var \GepurIt\ReportBundle\Helpers\RabbitHelper|MockObject $rabbitMock */
        $rabbitMock = $this->createMock(RabbitHelper::class);

        /** @var CreateReportCommandInterface|MockObject $commandMock */
        $commandMock = $this->createMock(CreateReportCommandInterface::class);
        $commandMock
            ->expects($this->at(0))
            ->method('setStatus')
            ->with(CreateReportCommandInterface::STATUS__IN_PROGRESS)
            ->willReturn(null);
        $commandMock
            ->expects($this->at(1))
            ->method('setStatus')
            ->with(CreateReportCommandInterface::STATUS__FINISHED)
            ->willReturn(null);

        /** @var ReportGeneratorInterface|MockObject $generatorMock */
        $generatorMock = $this->createMock(ReportGeneratorInterface::class);
        $generatorMock
            ->expects($this->once())
            ->method('generate')
            ->with($commandMock)
            ->willReturn([]);
        $generatorMock->expects($this->once())
            ->method('getKey')
            ->willReturn(get_class($commandMock));

        /** @var DocumentManager|MockObject $documentManagerMock */
        $documentManagerMock = $this->createMock(DocumentManager::class);
        $documentManagerMock
            ->expects($this->exactly(2))
            ->method('persist')
            ->with($commandMock)
            ->willReturn(null);
        $documentManagerMock
            ->expects($this->exactly(2))
            ->method('flush')
            ->with($commandMock)
            ->willReturn(null);

        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        $handler = new ReportCommandHandler(
            $documentManagerMock,
            $loggerMock,
            $rabbitMock
        );
        $handler->add($generatorMock);
        $res = $handler->process($commandMock);
        $this->assertTrue($res);
    }

    /**
     * @throws \Exception
     */
    public function testProcessException()
    {
        /** @var RabbitHelper|MockObject $rabbitMock */
        $rabbitMock = $this->createMock(RabbitHelper::class);
        /** @var CreateReportCommandInterface|MockObject $commandMock */
        $commandMock = $this->createMock(CreateReportCommandInterface::class);
        /** @var DocumentManager|MockObject $documentManagerMock */
        $documentManagerMock = $this->createMock(DocumentManager::class);
        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        $handler = new ReportCommandHandler(
            $documentManagerMock,
            $loggerMock,
            $rabbitMock
        );
        $generator = $this->createMock(ReportGeneratorInterface::class);
        $generator->expects($this->once())
            ->method('getKey')
            ->willReturn('trololo');
        $handler->add($generator);
        $this->expectException(GeneratorNotFoundException::class);
        $handler->process($commandMock);
    }

    /**
     * @throws \Exception
     */
    public function testProcessErrors()
    {
        $errors = [
            'error1',
            'error2'
        ];
        /** @var RabbitHelper|MockObject $rabbitMock */
        $rabbitMock = $this->createMock(RabbitHelper::class);

        /** @var CreateReportCommandInterface|MockObject $commandMock */
        $commandMock = $this->createMock(CreateReportCommandInterface::class);
        $commandMock
            ->expects($this->at(0))
            ->method('setStatus')
            ->with(CreateReportCommandInterface::STATUS__IN_PROGRESS)
            ->willReturn(null);
        $commandMock
            ->expects($this->at(1))
            ->method('setStatus')
            ->with(CreateReportCommandInterface::STATUS__ERROR)
            ->willReturn(null);
        $commandMock
            ->expects($this->at(0))
            ->method('addError')
            ->willReturn($errors[0])
            ->willReturn(null);
        $commandMock
            ->expects($this->at(1))
            ->method('addError')
            ->willReturn($errors[1])
            ->willReturn(null);

        /** @var ReportGeneratorInterface|MockObject $generatorMock */
        $generatorMock = $this->createMock(ReportGeneratorInterface::class);
        $generatorMock
            ->expects($this->once())
            ->method('generate')
            ->with($commandMock)
            ->willReturn($errors);
        $generatorMock->expects($this->once())
            ->method('getKey')
            ->willReturn(get_class($commandMock));

        /** @var DocumentManager|MockObject $documentManagerMock */
        $documentManagerMock = $this->createMock(DocumentManager::class);
        $documentManagerMock
            ->expects($this->exactly(2))
            ->method('persist')
            ->with($commandMock)
            ->willReturn(null);
        $documentManagerMock
            ->expects($this->exactly(2))
            ->method('flush')
            ->with($commandMock)
            ->willReturn(null);

        /** @var LoggerInterface|MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        $handler = new ReportCommandHandler(
            $documentManagerMock,
            $loggerMock,
            $rabbitMock
        );
        $handler->add($generatorMock);
        $res = $handler->process($commandMock);
        $this->assertNotTrue($res);
    }
}
