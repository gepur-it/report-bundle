<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 14:04
 */

namespace GepurIt\ReportBundle\ReportType\ReportDataTypes;

use GepurIt\ReportBundle\Exception\ReportDataTypeException;
use GepurIt\ReportBundle\ReportType\ReportDataTypeInterface;

/**
 * Class Date
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class Date implements ReportDataTypeInterface
{
    const NAME = 'date';

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
     * @return \DateTime
     */
    public function process($data)
    {
        try {
            $dateTime = new \DateTime($data);
        } catch (ReportDataTypeException $exception) {
            throw $exception;
        }

        return $dateTime;
    }
}
