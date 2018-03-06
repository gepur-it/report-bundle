<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 12:26
 */
namespace GepurIt\ReportBundle\ReportType\ReportDataTypes;

use GepurIt\ReportBundle\Exception\ReportDataTypeException;
use GepurIt\ReportBundle\ReportType\ReportDataTypeInterface;

/**
 * Class DateTime
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class DateTime implements ReportDataTypeInterface
{
    const NAME = 'datetime';

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
     * @throws ReportDataTypeException
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
