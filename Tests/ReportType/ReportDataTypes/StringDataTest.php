<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 01.02.18
 * Time: 10:25
 */

namespace GepurIt\ReportBundle\ReportType\ReportDataTypes;

use GepurIt\ReportBundle\DataType\StringData;
use PHPUnit\Framework\TestCase;

/**
 * Class StringDataTest
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class StringDataTest extends TestCase
{
    public function testGetTypeId()
    {
        $type = new StringData();
        $this->assertEquals('string', $type->getKey());
    }

    /**
     * @dataProvider dataProvider
     * @param $data
     */
    public function testProcess($data)
    {
        $type = new StringData();
        $this->assertTrue(is_string($type->process($data)));
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            ['1'],
            [1],
            [3.1415926535897932384626433832795],
        ];
    }

    /**
     * @dataProvider exceptionDataProvider
     * @param $data
     */
    public function textException($data)
    {
        $type = new StringData();
        $this->expectException(\Exception::class);
        $type->process($data);
    }

    /**
     * @return array
     */
    public function exceptionDataProvider()
    {
        return [
            [[]],
            [new \stdClass()]
        ];
    }
}
