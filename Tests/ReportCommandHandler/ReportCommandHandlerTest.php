<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 01.12.17
 */

namespace GepurIt\ReportBundle\Tests\ReportCommandHandler;

use AMQPExchange;
use Doctrine\ODM\MongoDB\DocumentManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use GepurIt\ReportBundle\CreateCommand\CreateReportCommandInterface;
use GepurIt\ReportBundle\Exception\GeneratorNotFoundException;
use GepurIt\ReportBundle\ReportCommandHandler\RabbitHelper;
use GepurIt\ReportBundle\ReportCommandHandler\ReportCommandHandler;
use GepurIt\ReportBundle\ReportGenerator\ReportGeneratorInterface;

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
        /** @var CreateReportCommandInterface|\PHPUnit_Framework_MockObject_MockObject $commandMock */
        $commandMock = $this->createMock(CreateReportCommandInterface::class);
        $commandMock
            ->expects($this->once())
            ->method('getCommandId')
            ->willReturn('stringId');

        /** @var DocumentManager|\PHPUnit_Framework_MockObject_MockObject $documentManagerMock */
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

        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        /** @var AMQPExchange|\PHPUnit_Framework_MockObject_MockObject $exchangeMock */
        $exchangeMock = $this->createMock(AMQPExchange::class);

        /** @var RabbitHelper|\PHPUnit_Framework_MockObject_MockObject $rabbitMock */
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
     * @throws \ReflectionException
     */
    public function testProcessSuccess()
    {
        /** @var RabbitHelper|\PHPUnit_Framework_MockObject_MockObject $rabbitMock */
        $rabbitMock = $this->createMock(RabbitHelper::class);

        /** @var CreateReportCommandInterface|\PHPUnit_Framework_MockObject_MockObject $commandMock */
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

        /** @var ReportGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject $generatorMock */
        $generatorMock = $this->createMock(ReportGeneratorInterface::class);
        $generatorMock
            ->expects($this->once())
            ->method('generate')
            ->with($commandMock)
            ->willReturn([]);

        /** @var DocumentManager|\PHPUnit_Framework_MockObject_MockObject $documentManagerMock */
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

        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        $handler = new ReportCommandHandler(
            $documentManagerMock,
            $loggerMock,
            $rabbitMock
        );
        $handler->addGenerator(get_class($commandMock), $generatorMock);
        $res = $handler->process($commandMock);
        $this->assertTrue($res);
    }

    /**
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function testProcessException()
    {
        /** @var RabbitHelper|\PHPUnit_Framework_MockObject_MockObject $rabbitMock */
        $rabbitMock = $this->createMock(RabbitHelper::class);
        /** @var CreateReportCommandInterface|\PHPUnit_Framework_MockObject_MockObject $commandMock */
        $commandMock = $this->createMock(CreateReportCommandInterface::class);
        /** @var DocumentManager|\PHPUnit_Framework_MockObject_MockObject $documentManagerMock */
        $documentManagerMock = $this->createMock(DocumentManager::class);
        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        $handler = new ReportCommandHandler(
            $documentManagerMock,
            $loggerMock,
            $rabbitMock
        );
        $this->expectException(GeneratorNotFoundException::class);
        $handler->process($commandMock);
    }

    /**
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function testProcessErrors()
    {
        $errors = [
            'error1',
            'error2'
        ];
        /** @var RabbitHelper|\PHPUnit_Framework_MockObject_MockObject $rabbitMock */
        $rabbitMock = $this->createMock(RabbitHelper::class);

        /** @var CreateReportCommandInterface|\PHPUnit_Framework_MockObject_MockObject $commandMock */
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

        /** @var ReportGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject $generatorMock */
        $generatorMock = $this->createMock(ReportGeneratorInterface::class);
        $generatorMock
            ->expects($this->once())
            ->method('generate')
            ->with($commandMock)
            ->willReturn($errors);

        /** @var DocumentManager|\PHPUnit_Framework_MockObject_MockObject $documentManagerMock */
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

        /** @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $loggerMock */
        $loggerMock = $this->createMock(LoggerInterface::class);

        $handler = new ReportCommandHandler(
            $documentManagerMock,
            $loggerMock,
            $rabbitMock
        );
        $handler->addGenerator(get_class($commandMock), $generatorMock);
        $res = $handler->process($commandMock);
        $this->assertNotTrue($res);
    }
}
