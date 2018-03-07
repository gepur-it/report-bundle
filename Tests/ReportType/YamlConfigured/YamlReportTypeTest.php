<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 01.02.18
 * Time: 10:21
 */
namespace GepurIt\ReportBundle\ReportType\YamlConfigured;

use PHPUnit\Framework\TestCase;

/**
 * Class YamlReportTypeTest
 * @package ReportBundle\ReportType\YamlConfigured
 */
class YamlReportTypeTest extends TestCase
{
    public function testScalarGetters()
    {
        $className = 'someClassName';
        $type = new YamlReportType('typeId', $this->getConfig());
        $this->assertEquals('typeId', $type->getTypeId());
        $this->assertEquals('label', $type->getLabel());
        $this->assertEquals('resource', $type->getResource());
        $this->assertEquals('role', $type->getRole());
        $this->assertEquals($className, $type->getReportClass());
        $this->assertEquals([], $type->getReportMeta());
        $this->assertEquals([], $type->getCommandMeta());
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'type' => [
                'label' => 'label',
                'resource' => 'resource',
                'role' => 'role',
                'report_class' => 'someClassName',

            ],
            'report_meta' => [],
            'command_meta' => []
        ];
    }
}
