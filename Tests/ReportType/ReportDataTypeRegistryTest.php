<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 01.02.18
 * Time: 10:04
 */

namespace GepurIt\ReportBundle\ReportType;

use PHPUnit\Framework\TestCase;
use GepurIt\ReportBundle\Exception\ReportException;

/**
 * Class ReportDataTypeRegistryTest
 * @package ReportBundle\ReportType
 */
class ReportDataTypeRegistryTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testRegisterType()
    {
        /** @var ReportDataTypeInterface|\PHPUnit_Framework_MockObject_MockObject $type */
        $type = $this->createMock(ReportDataTypeInterface::class);

        $registry = new ReportDataTypeRegistry();

        $type->expects($this->once())
            ->method('getTypeId')
            ->willReturn('type_key');

        $registry->register($type);
    }

    /**
     * @throws \ReflectionException
     */
    public function testNotGetType()
    {
        $registry = new ReportDataTypeRegistry();

        /** @var ReportDataTypeInterface|\PHPUnit_Framework_MockObject_MockObject $type */
        $type = $this->createMock(ReportDataTypeInterface::class);

        $type->expects($this->once())
            ->method('getTypeId')
            ->willReturn('type_key');

        $registry->register($type);

        $this->expectException(ReportException::class);
        $result = $registry->get('type_key2');

        $this->assertInstanceOf(ReportDataTypeInterface::class, $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetAcceptType()
    {
        $registry = new ReportDataTypeRegistry();

        /** @var ReportDataTypeInterface|\PHPUnit_Framework_MockObject_MockObject $type */
        $type = $this->createMock(ReportDataTypeInterface::class);

        $type->expects($this->once())
            ->method('getTypeId')
            ->willReturn('type_key');

        $registry->register($type);

        $result = $registry->get('type_key');

        $this->assertInstanceOf(ReportDataTypeInterface::class, $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetTypes()
    {
        /** @var ReportDataTypeInterface|\PHPUnit_Framework_MockObject_MockObject $type */
        $type = $this->createMock(ReportDataTypeInterface::class);

        $registry = new ReportDataTypeRegistry();

        $type->expects($this->once())
            ->method('getTypeId')
            ->willReturn('type_key');

        $registry->register($type);

        $result = $registry->get('type_key');
        $this->assertInstanceOf(ReportDataTypeInterface::class, $result);
    }
}
