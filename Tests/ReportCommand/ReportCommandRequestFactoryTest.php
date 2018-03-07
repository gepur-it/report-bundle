<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 01.02.18
 * Time: 16:25
 */

namespace GepurIt\ReportBundle\Tests\ReportCommand;

use DateTime;
use GepurIt\ReportBundle\CreateCommand\CreateReportCommandInterface;
use GepurIt\ReportBundle\CreateCommand\ReportCommandRequestFactory;
use GepurIt\ReportBundle\DataType\ReportDataTypeInterface;
use GepurIt\ReportBundle\ReportType\ReportTypeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Yawa20\RegistryBundle\Registrable\RegistrableInterface;
use Yawa20\RegistryBundle\Registry\SimpleRegistry;

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
        /** @var SimpleRegistry|\PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this->createMock(SimpleRegistry::class);
        /** @var PropertyAccessor|\PHPUnit_Framework_MockObject_MockObject $propertyAccessor */
        $propertyAccessor = $this->createMock(PropertyAccessor::class);
        /** @var ReportTypeInterface|\PHPUnit_Framework_MockObject_MockObject $reportType */
        $reportType = $this->createMock(ReportTypeInterface::class);
        $dataType = new TestDataType();

        $registry->expects($this->once())
            ->method('get')
            ->with('string')
            ->willReturn($dataType);

        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->createMock(Request::class);

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
        /** @var SimpleRegistry|\PHPUnit_Framework_MockObject_MockObject $registry */
        $registry = $this->createMock(SimpleRegistry::class);
        /** @var PropertyAccessor|\PHPUnit_Framework_MockObject_MockObject $propertyAccessor */
        $propertyAccessor = $this->createMock(PropertyAccessor::class);
        /** @var ReportTypeInterface|\PHPUnit_Framework_MockObject_MockObject $reportType */
        $reportType = $this->createMock(ReportTypeInterface::class);
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->createMock(Request::class);

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

class TestDataType implements RegistrableInterface, ReportDataTypeInterface
{
    /**
     * @return string
     */
    public function getKey(): string
    {
        return 'string';
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function process($data)
    {
        return $data;
    }
}
