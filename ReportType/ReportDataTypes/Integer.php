<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 13:14
 */

namespace GepurIt\ReportBundle\ReportType\ReportDataTypes;

use GepurIt\ReportBundle\ReportType\ReportDataTypeInterface;

/**
 * Class Integer
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class Integer implements ReportDataTypeInterface
{
    const NAME = 'integer';

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
        return (int)$data;
    }
}
