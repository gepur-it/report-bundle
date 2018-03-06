<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 01.02.18
 * Time: 10:23
 */

namespace GepurIt\ReportBundle\ReportType\ReportDataTypes;

use PHPUnit\Framework\TestCase;

/**
 * Class DateTest
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class DateTest extends TestCase
{
    public function testGetTypeId()
    {
        $type = new Date();
        $this->assertEquals('date', $type->getTypeId());
    }

    public function testProcess()
    {
        $type = new Date();

        $this->assertInstanceOf(\DateTime::class, $type->process((new \DateTime('now'))->format('Y-m-d')));
        $this->assertInstanceOf(\DateTime::class, $type->process((new \DateTime('now'))->format('Y-m-d h:i')));
        $this->assertInstanceOf(\DateTime::class, $type->process((new \DateTime('now'))->format('Y-M-d')));
        $this->assertInstanceOf(\DateTime::class, $type->process((new \DateTime('now'))->format('h:i')));

        $this->expectException(\Exception::class);
        $type->process('To those whom much is given, much is lost');
    }
}
