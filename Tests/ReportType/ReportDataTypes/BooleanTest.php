<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 01.02.18
 * Time: 10:22
 */
namespace GepurIt\ReportBundle\ReportType\ReportDataTypes;

use GepurIt\ReportBundle\DataType\Boolean;
use PHPUnit\Framework\TestCase;

/**
 * Class BooleanTest
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class BooleanTest extends TestCase
{
    public function testGetTypeId()
    {
        $type = new Boolean();
        $this->assertEquals('boolean', $type->getKey());
    }

    /**
     * @dataProvider dataProvider
     * @param $expectation
     * @param $value
     */
    public function testProcess($expectation, $value)
    {
        $type = new Boolean();

        $this->assertEquals($expectation, $type->process($value));
    }

    /**
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            [false, 'false'],
            [false, false],
            [false, null],
            [false, 0],
            [true, 'true'],
            [true, 1],
            [true, true],
            [true, ['somekey']],
            [true, new \stdClass()],
        ];
    }
}
