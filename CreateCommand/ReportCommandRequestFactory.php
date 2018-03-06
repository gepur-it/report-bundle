<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 14:19
 */

namespace GepurIt\ReportBundle\CreateCommand;

use GepurIt\ReportBundle\ReportType\ReportDataTypeRegistry;
use GepurIt\ReportBundle\ReportType\ReportTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class ReportTypeFactory
 * @package ReportBundle\ReportType
 */
class ReportCommandRequestFactory
{
    /**
     * @var ReportDataTypeRegistry
     */
    private $registry;

    /** @var PropertyAccessor  */
    private $accessor;

    /**
     * ReportCommandRequestFactory constructor.
     * @param ReportDataTypeRegistry $registry
     * @param PropertyAccessor $accessor
     */
    public function __construct(ReportDataTypeRegistry $registry, PropertyAccessor $accessor)
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
            $fieldData = $this->registry->get($type)->process($request->get($field['field'], $default));
            $this->accessor->setValue($command, $field['field'], $fieldData);
        }

        return $command;
    }
}
