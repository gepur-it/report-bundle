<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 01.02.18
 * Time: 10:25
 */

namespace GepurIt\ReportBundle\ReportType\ReportDataTypes;

use PHPUnit\Framework\TestCase;

/**
 * Class IntegerTest
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class IntegerTest extends TestCase
{
    public function testGetTypeId()
    {
        $type = new Integer();
        $this->assertEquals('integer', $type->getTypeId());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testProcess($data)
    {
        $type = new Integer();
        $this->assertTrue(is_integer($type->process($data)));
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
            [[]]
        ];
    }
}
