<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since: 24.01.18
 */

namespace GepurIt\ReportBundle\ReportType\YamlConfigured;

use GepurIt\ReportBundle\ReportType\ReportTypeInterface;
use Yawa20\RegistryBundle\Registrable\RegistrableInterface;

/**
 * Class YamlConfiguredReportType
 * @package ReportBundle\ReportType
 */
class YamlReportType implements ReportTypeInterface, RegistrableInterface
{
    /**
     * @var string $typeId
     */
    protected $typeId;

    /**
     * @var string $label
     */
    protected $label;

    /**
     * @var string $resource
     */
    protected $resource;

    /**
     * @var string $role
     */
    protected $role;

    /**
     * @var array $commandMeta
     */
    protected $commandMeta;

    /**
     * @var array $reportMeta
     */
    protected $reportMeta;

    /**
     * @var string $reportClass
     */
    protected $reportClass;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $group = 'default';

    /**
     * AbstractReportType constructor.
     * @param string $typeId
     * @param array $config
     */
    public function __construct(string $typeId, array $config)
    {
        $this->typeId = $typeId;
        $this->init($config);
    }

    /**
     * @return string
     */
    public function getTypeId(): string
    {
        return $this->typeId;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return array
     */
    public function getCommandMeta(): array
    {
        return $this->commandMeta;
    }

    /**
     * @return array
     */
    public function getReportMeta(): array
    {
        return $this->reportMeta;
    }

    /**
     * @return string
     */
    public function getReportClass(): string
    {
        return $this->reportClass;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param array $config
     */
    protected function init(array $config)
    {
        $this->label = $config['type']['label'];
        $this->resource = $config['type']['resource'];
        $this->role = $config['type']['role'];
        $this->commandMeta = $config['command_meta'];
        $this->reportMeta = $config['report_meta'];
        $this->reportClass = $config['type']['report_class'];
        $this->group = ($config['type']['group'])??$this->group;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->getTypeId();
    }
}
