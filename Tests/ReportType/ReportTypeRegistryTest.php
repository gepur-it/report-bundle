<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 09.01.18
 * Time: 10:17
 */

namespace GepurIt\ReportBundle\ReportType;

use PHPUnit\Framework\TestCase;
use GepurIt\ReportBundle\Exception\ReportException;

/**
 * Class ReportTypeRegistryTest
 * @package ReportBundle\ReportType
 */
class ReportTypeRegistryTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testRegisterType()
    {
        /** @var ReportTypeInterface|\PHPUnit_Framework_MockObject_MockObject $type */
        $type = $this->createMock(ReportTypeInterface::class);

        $registry = new ReportTypeRegistry();

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
        $registry = new ReportTypeRegistry();

        /** @var ReportTypeInterface|\PHPUnit_Framework_MockObject_MockObject $type */
        $type = $this->createMock(ReportTypeInterface::class);

        $type->expects($this->once())
            ->method('getTypeId')
            ->willReturn('type_key');

        $registry->register($type);

        $this->expectException(ReportException::class);
        $result = $registry->get('type_key2');

        $this->assertInstanceOf(ReportTypeInterface::class, $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetAcceptType()
    {
        $registry = new ReportTypeRegistry();

        /** @var ReportTypeInterface|\PHPUnit_Framework_MockObject_MockObject $type */
        $type = $this->createMock(ReportTypeInterface::class);

        $type->expects($this->once())
            ->method('getTypeId')
            ->willReturn('type_key');

        $registry->register($type);

        $result = $registry->get('type_key');

        $this->assertInstanceOf(ReportTypeInterface::class, $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetTypes()
    {
        /** @var ReportTypeInterface|\PHPUnit_Framework_MockObject_MockObject $type */
        $type = $this->createMock(ReportTypeInterface::class);

        $registry = new ReportTypeRegistry();

        $type->expects($this->once())
            ->method('getTypeId')
            ->willReturn('type_key');

        $registry->register($type);

        $result = $registry->getTypes();
        $this->assertArrayHasKey('type_key', $result);
    }
}
