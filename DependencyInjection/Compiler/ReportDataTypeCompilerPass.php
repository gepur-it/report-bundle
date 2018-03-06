<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 12:30
 */

namespace GepurIt\ReportBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ReportDataTypeCompilerPass
 * @package ReportBundle\DependencyInjection\Compiler
 */
class ReportDataTypeCompilerPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'report.data_type_registry';
    const ITEM_TAG = 'report_data_type';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $definition = $container->findDefinition(self::SERVICE_NAME);
        $taggedServices = $container->findTaggedServiceIds(self::ITEM_TAG);

        foreach ($taggedServices as $key => $tags) {
            $reportType = $container->getDefinition($key);
            $definition->addMethodCall('register', [$reportType]);
        }
    }
}