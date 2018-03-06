<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 09.01.18
 * Time: 9:53
 */

namespace GepurIt\ReportBundle\ReportType;

/**
 * Interface ReportTypeCommandInterface
 * @package ReportBundle\ReportType
 */
interface ReportTypeCommandRepositoryInterface
{
    /**
     * @param array $fields
     * @param int $limit
     * @param int $skip
     * @return array
     */
    public function findForType(array $fields, int $limit, int $skip) : array;

    /**
     * @return int
     */
    public function count() :int;
}
