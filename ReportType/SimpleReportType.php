<?php
/**
 * Created by PhpStorm.
 * User: marina mileva m934222258@gmail.com
 * Date: 17.01.18
 * Time: 14:39
 */

namespace GepurIt\ReportBundle\ReportType;

/**
 * Class SimpleReportType
 * @package ReportBundle\ReportType
 * @JMS\ExclusionPolicy("all")
 */
class SimpleReportType implements ReportTypeInterface
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
     * @var \array $commandMeta
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
     * @var string
     * @JMS\Expose()
     */
    protected $group = 'default';

    /**
     * AbstractReportType constructor.
     * @param string $typeId
     * @param string $label
     * @param string $resource
     * @param string $role
     * @param string $reportClass
     */
    public function __construct(string $typeId, string $label, string $resource, string $role, string $reportClass)
    {
        $this->typeId = $typeId;
        $this->label = $label;
        $this->resource = $resource;
        $this->role = $role;
        $this->reportClass = $reportClass;
    }

    /**
     * @return string
     */
    public function getTypeId() :string
    {
        return $this->typeId;
    }

    /**
     * @return string
     */
    public function getLabel() :string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getResource() :string
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getRole() :string
    {
        return $this->role;
    }

    /**
     * @param \stdClass $commandMeta
     */
    public function setCommandMeta(\stdClass $commandMeta) :void
    {
        $this->commandMeta = $commandMeta;
    }

    /**
     * @return array
     */
    public function getCommandMeta() :array
    {
        if (null !== $this->commandMeta) {
            return $this->commandMeta;
        }
        $class = $this->getReplacedClass('CommandMeta');
        $this->commandMeta = (array) new $class();

        return $this->commandMeta;
    }

    /**
     * @param array $reportMeta
     */
    public function setReportMeta(array $reportMeta) :void
    {
        $this->reportMeta = $reportMeta;
    }

    /**
     * @return array
     */
    public function getReportMeta(): array
    {
        if (null !== $this->reportMeta) {
            return $this->reportMeta;
        }
        $class = $this->getReplacedClass('ReportMeta');
        $this->reportMeta = (array) new $class();

        return $this->reportMeta;
    }

    /**
     * @return string
     */
    public function getReportClass(): string
    {
        return $this->reportClass;
    }

    protected function getReplacedClass($metaClassName): string
    {
        $class = preg_replace('/([^\\\]+)$/', '', $this->getReportClass());
        $class .= $metaClassName;
        return $class;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup(string $group) :void
    {
        $this->group = $group;
    }
}