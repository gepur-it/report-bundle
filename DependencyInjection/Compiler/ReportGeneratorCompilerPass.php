<?php

namespace GepurIt\ReportBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ReportGeneratorCompilerPass
 * @package ReportBundle\DependencyInjection\Compiler
 * @codeCoverageIgnore
 */
class ReportGeneratorCompilerPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'report_handler';
    const ITEM_TAG = 'report_generator';

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
            $reportGenerator = $container->getDefinition($key);
            foreach ($tags as $tag) {
                $command = $tag['command'];
                $definition->addMethodCall('addGenerator', [$command, $reportGenerator]);
            }
        }
    }
}
