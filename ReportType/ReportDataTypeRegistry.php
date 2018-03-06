<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 12:14
 */

namespace GepurIt\ReportBundle\ReportType;

use GepurIt\ReportBundle\Exception\ReportException;

/**
 * Class ReportDataTypeRegistry
 * @package ReportBundle\ReportType
 */
class ReportDataTypeRegistry
{
    /**
     * @var ReportDataTypeInterface[]
     */
    private $registered = [];

    /**
     * @param ReportDataTypeInterface $dataType
     */
    public function register(ReportDataTypeInterface $dataType): void
    {
        $this->registered[$dataType->getTypeId()] = $dataType;
    }

    /**
     * @param string $dataId
     * @return ReportDataTypeInterface
     */
    public function get(string $dataId) :ReportDataTypeInterface
    {
        if (false === array_key_exists($dataId, $this->registered)) {
            throw new ReportException('The report data type with id='.$dataId.' not exists');
        }

        return $this->registered[$dataId];
    }
}
