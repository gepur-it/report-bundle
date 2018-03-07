<?php

namespace GepurIt\ReportBundle\DependencyInjection;

use GepurIt\ReportBundle\ReportType\YamlConfigured\YamlReportType;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension as BaseExtension;
use Symfony\Component\Yaml\Yaml;

/**
 * @package ReportBundle\DependencyInjection
 * @codeCoverageIgnore
 */
class ReportExtension extends BaseExtension
{
    /** @var ContainerBuilder */
    private $container;

    /**
     * Loads a specific configuration.
     *
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $reportPaths = $config['reports_path'];
        $autoFound = $this->autoSearchTypes($reportPaths, $config['types']);
        $types = array_merge($config['types'], $autoFound);
        $this->addTypes($reportPaths, $types);
    }

    /**
     * automatically search yaml types
     * @param string $path
     * @param array $types
     * @return array
     */
    protected function autoSearchTypes(string $path, array $types)
    {
        $result = [];
        $finder = new Finder();
        $finder->directories()->in($path);
        foreach ($finder as $directory) {
            $reportDirectoryName = $directory->getBasename();
            $ucName = trim(strtolower(preg_replace('/[A-Z]/', '_\\0', $reportDirectoryName)), '_');
            if (array_key_exists($ucName, $types)) {
                continue;
            }
            $configPath = $directory->getRealPath().DIRECTORY_SEPARATOR.'config.yml';
            if (!is_file($configPath)) {
                continue;
            }
            $result[$ucName] = ['folder' => $reportDirectoryName, 'definition' => 'yaml'];
        }

        return $result;
    }

    protected function addTypes($paths, $types)
    {
        $registryDefinition = $this->container->getDefinition('report.type_registry');

        foreach ($types as $name => $typeConfig) {
            switch ($typeConfig['definition']) {
                case 'service':
                    if (!isset($typeConfig['service'])) {
                        $message = "the 'service' key is required for report type 'service' definition";
                        throw new InvalidConfigurationException($message);
                    }
                    $definition = $this->getServiceReportTypeDefinition($typeConfig['service']);
                    break;
                case 'yaml':
                default:
                    $definition = $this->getYamlReportTypeDefinition($name, $paths, $typeConfig);
            }
            $registryDefinition->addMethodCall('add', [$definition]);
        }
    }

    /**
     * @param string $name
     * @param string $paths
     * @param array $config
     * @return Definition
     */
    protected function getYamlReportTypeDefinition(string $name, string $paths, array $config): Definition
    {

        $definitionName = 'report.type.'.$name;
        $definition = $this->container->setDefinition($definitionName, new Definition(YamlReportType::class));
        $definition->addArgument($name);

        $folder = $config['folder']??str_replace('_', '', ucwords($name, "-_\t\r\n\f\v"));
        $locator = new FileLocator($paths.'/'.$folder);
        $configPath = $locator->locate('config.yml');
        $definition->addArgument(Yaml::parseFile($configPath));

        return $definition;
    }

    /**
     * @param string $serviceName
     * @return Definition
     */
    protected function getServiceReportTypeDefinition(string $serviceName): Definition
    {
        $definition = $this->container->getDefinition($serviceName);

        return $definition;
    }
}
