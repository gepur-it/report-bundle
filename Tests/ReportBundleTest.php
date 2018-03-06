<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 01.12.17
 */

namespace GepurIt\ReportBundle\Tests;

use PHPUnit\Framework\TestCase;
use GepurIt\ReportBundle\DependencyInjection\Compiler\ReportGeneratorCompilerPass;
use GepurIt\ReportBundle\DependencyInjection\Compiler\ReportTypeCompilerPass;
use GepurIt\ReportBundle\DependencyInjection\ReportExtension;
use GepurIt\ReportBundle\ReportBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ReportBundleTest
 * @package ReportBundle\Tests
 */
class ReportBundleTest extends TestCase
{
    public function testBundle()
    {
        $containerBuilder = $this->getContainerMock();

        $containerBuilder->expects($this->at(0))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(ReportGeneratorCompilerPass::class));

        $containerBuilder->expects($this->at(1))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(ReportTypeCompilerPass::class));

        $bundle = new ReportBundle();
        $bundle->build($containerBuilder);
    }

    public function testGetContainerExtension()
    {
        $bundle = new ReportBundle();
        $extension = $bundle->getContainerExtension();
        $this->assertInstanceOf(ReportExtension::class, $extension);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject | ContainerBuilder
     */
    public function getContainerMock()
    {
        return  $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'addCompilerPass',
            ])
            ->getMock();
    }
}
