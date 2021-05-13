<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 14:19
 */

namespace GepurIt\ReportBundle\CreateCommand;

use GepurIt\ReportBundle\DataType\ReportDataTypeInterface;
use GepurIt\ReportBundle\ReportType\ReportTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Yawa20\RegistryBundle\Registry\RegistryInterface;

/**
 * Class ReportTypeFactory
 * @package ReportBundle\ReportType
 */
class ReportCommandRequestFactory
{
    private RegistryInterface $registry;
    private PropertyAccessor $accessor;

    /**
     * ReportCommandRequestFactory constructor.
     * @param RegistryInterface $registry
     * @param PropertyAccessor $accessor
     */
    public function __construct(RegistryInterface $registry, PropertyAccessor $accessor)
    {
        $this->registry = $registry;
        $this->accessor = $accessor;
    }

    /**
     * @param ReportTypeInterface $reportType
     * @param Request $request
     * @return CreateReportCommandInterface
     * @throws \TypeError
     */
    public function create(ReportTypeInterface $reportType, Request $request) :CreateReportCommandInterface
    {
        $commandClass = $reportType->getReportClass();
        $fields = (array) $reportType->getCommandMeta();

        /** @var CreateReportCommandInterface $command */
        $command = new $commandClass;

        foreach ($fields as $field) {
            if (!$this->accessor->isWritable($command, $field['field'])) {
                continue;
            }
            $default = $field['default'] ?? null;
            $type = $field['persist_type'] ?? $field['type'];
            /** @var ReportDataTypeInterface $dataType */
            $dataType = $this->registry->get($type);
            $fieldData = $dataType->process($request->get($field['field'], $default));
            $this->accessor->setValue($command, $field['field'], $fieldData);
        }

        return $command;
    }
}
