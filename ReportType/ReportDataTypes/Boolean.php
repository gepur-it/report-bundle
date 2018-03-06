<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 14:00
 */

namespace GepurIt\ReportBundle\ReportType\ReportDataTypes;

use GepurIt\ReportBundle\ReportType\ReportDataTypeInterface;

/**
 * Class Boolean
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class Boolean implements ReportDataTypeInterface
{
    const NAME = 'boolean';
    /**
     * @var string $typeId
     */
    protected $typeId;

    /**
     * @return string
     */
    public function getTypeId() :string
    {
        return self::NAME;
    }

    /**
     * @param mixed $data
     * @return int
     */
    public function process($data)
    {
        $falsest = ['null', 'undefined', 'false', '0'];
        if (is_array($data) || is_object($data)) {
            return !empty($data);
        }
        if (empty($data) || in_array(strtolower($data), $falsest)) {
            $data = false;
        }

        return (bool) $data;
    }
}
