<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 01.02.18
 * Time: 16:25
 */

namespace GepurIt\ReportBundle\Tests\ReportCommand;

use DateTime;
use GepurIt\ReportBundle\CreateCommand\ReportCommandRequestFactory;
use PHPUnit\Framework\TestCase;
use GepurIt\ReportBundle\CreateCommand\CreateReportCommandInterface;
use GepurIt\ReportBundle\ReportType\ReportDataTypeRegistry;
use GepurIt\ReportBundle\ReportType\ReportTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class ReportCommandRequestFactoryTest
 * @package ReportBundle\Tests\ReportCommand
 */
class ReportCommandRequestFactoryTest extends TestCase
{
    /**
     * @throws \ReflectionException
     * @throws \TypeError
     */
    public function testCreateWritable()
    {
        /** @var ReportDataTypeRegistry|\PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this->createMock(ReportDataTypeRegistry::class);
        /** @var PropertyAccessor|\PHPUnit_Framework_MockObject_MockObject $propertyAccessor */
        $propertyAccessor = $this->createMock(PropertyAccessor::class);
        /** @var ReportTypeInterface|\PHPUnit_Framework_MockObject_MockObject $reportType */
        $reportType = $this->createMock(ReportTypeInterface::class);
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->createMock(Request::class);;

        $factory = new ReportCommandRequestFactory($registry, $propertyAccessor);

        $reportType->expects($this->once())->method('getReportClass')->willReturn(TestReportCreateCommand::class);
        $reportType->expects($this->once())->method('getCommandMeta')->willReturn([
            'field' => [
                'field' => 'field',
                'default' => '',
                'persist_type' => 'string',
            ]
        ]);

        $propertyAccessor->expects($this->once())->method('isWritable')->willReturn(true);

        $factory->create($reportType, $request);
    }

    /**
     * @throws \ReflectionException
     * @throws \TypeError
     */
    public function testCreateNotWritable()
    {
        /** @var ReportDataTypeRegistry|\PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this->createMock(ReportDataTypeRegistry::class);
        /** @var PropertyAccessor|\PHPUnit_Framework_MockObject_MockObject $propertyAccessor */
        $propertyAccessor = $this->createMock(PropertyAccessor::class);
        /** @var ReportTypeInterface|\PHPUnit_Framework_MockObject_MockObject $reportType */
        $reportType = $this->createMock(ReportTypeInterface::class);
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->createMock(Request::class);;

        $factory = new ReportCommandRequestFactory($registry, $propertyAccessor);

        $reportType->expects($this->once())->method('getReportClass')->willReturn(TestReportCreateCommand::class);
        $reportType->expects($this->once())->method('getCommandMeta')->willReturn([
            'field' => [
                'field' => 'field',
                'default' => '',
                'persist_type' => 'string',
            ]
        ]);

        $propertyAccessor->expects($this->once())->method('isWritable')->willReturn(false);

        $factory->create($reportType, $request);
    }
}

/**
 * Class TestReportCreateCommand
 * @package ReportBundle\Tests\ReportCommand
 */
class TestReportCreateCommand implements CreateReportCommandInterface
{
    /** @return string */
    public function getCommandId(): string
    {
        return 'ololo';
    }

    /** @return DateTime*/
    public function getCreatedAt(): DateTime
    {
        return new DateTime();
    }

    /** @param int $status */
    public function setStatus(int $status)
    {

    }

    /** @return int */
    public function getStatus(): int
    {
        return 0;
    }

    /** @return array */
    public function getErrors(): array
    {
        return [];
    }

    /** @param $message string */
    public function addError($message)
    {

    }
}