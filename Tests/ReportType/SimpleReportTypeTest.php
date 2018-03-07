<?php
/**
 * Created by PhpStorm.
 * User: marina mileva m934222258@gmail.com
 * Date: 19.01.18
 * Time: 12:25
 */

namespace GepurIt\ReportBundle\Tests\ReportType;

use GepurIt\ReportBundle\Document\AbstractCreateReportCommand;
use GepurIt\ReportBundle\ReportType\SimpleReportType;
use PHPUnit\Framework\TestCase;

/**
 * Class SimpleReportTypeTest
 * @package ReportBundle\Tests\ReportType
 */
class SimpleReportTypeTest extends TestCase
{
    public function testScalarGetters()
    {
        $className = TestReportCommand::class;
        $type = new SimpleReportType('typeId', 'label', 'resource', 'role', $className);
        $this->assertEquals('typeId', $type->getTypeId());
        $this->assertEquals('label', $type->getLabel());
        $this->assertEquals('resource', $type->getResource());
        $this->assertEquals('role', $type->getRole());
        $type->setReportMeta([]);
        $this->assertEquals([], $type->getReportMeta());
        $this->assertEquals(['test' => 'testCommandValue'], $type->getCommandMeta());
        $this->assertEquals($className, $type->getReportClass());
    }

    public function testGetCommandMeta()
    {
        $className = TestReportCommand::class;
        $type = new SimpleReportType('typeId', 'label', 'resource', 'role', $className);
        $commandMetaSource = new CommandMeta();
        $commandMeta = $type->getCommandMeta();
        $this->assertEquals($commandMetaSource->test, $commandMeta['test']);
    }

    public function testGetReportMeta()
    {
        $className = TestReportCommand::class;
        $type = new SimpleReportType('typeId', 'label', 'resource', 'role', $className);
        $reportMetaSource = new ReportMeta();
        $reportMeta = $type->getReportMeta();
        $this->assertEquals($reportMetaSource->test, $reportMeta['test']);
    }
}

/**
 * Class TestReportCommand
 * @package ReportBundle\Tests\ReportType
 */
class TestReportCommand extends AbstractCreateReportCommand
{

}

/**
 * Class CommandMeta
 * @package ReportBundle\Tests\ReportType
 */
class CommandMeta extends \stdClass
{
    public $test = 'testCommandValue';
}

/**
 * Class ReportMeta
 * @package ReportBundle\Tests\ReportType
 */
class ReportMeta extends \stdClass
{
  public $test = 'testReportValue';
}