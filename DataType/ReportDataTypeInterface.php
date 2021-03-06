<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 12:14
 */

namespace GepurIt\ReportBundle\DataType;

/**
 * Interface ReportDataTypeInterface
 * @package ReportBundle\ReportType
 */
interface ReportDataTypeInterface
{
    /**
     * @param mixed $data
     * @return mixed
     */
    public function process($data);
}
