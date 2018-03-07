<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 01.02.18
 * Time: 10:24
 */

namespace GepurIt\ReportBundle\ReportType\ReportDataTypes;

use GepurIt\ReportBundle\DataType\DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeTest
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class DateTimeTest extends TestCase
{
    public function testGetTypeId()
    {
        $type = new DateTime();
        $this->assertEquals('datetime', $type->getKey());
    }

    public function testProcess()
    {
        $type = new DateTime();

        $this->assertInstanceOf(\DateTime::class, $type->process((new \DateTime('now'))->format('Y-m-d')));
        $this->assertInstanceOf(\DateTime::class, $type->process((new \DateTime('now'))->format('Y-m-d h:i')));
        $this->assertInstanceOf(\DateTime::class, $type->process((new \DateTime('now'))->format('Y-M-d')));
        $this->assertInstanceOf(\DateTime::class, $type->process((new \DateTime('now'))->format('h:i')));

        $this->expectException(\Exception::class);
        $type->process('To those whom much is given, much is lost');
    }
}
