<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 14:04
 */

namespace GepurIt\ReportBundle\DataType;

use GepurIt\ReportBundle\Exception\ReportDataTypeException;
use Yawa20\RegistryBundle\Registrable\RegistrableInterface;

/**
 * Class Date
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class Date implements ReportDataTypeInterface, RegistrableInterface
{
    const NAME = 'date';

    /**
     * @var string $typeId
     */
    protected $typeId;

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

    /**
     * @return string
     */
    public function getKey(): string
    {
        return self::NAME;
    }
}
