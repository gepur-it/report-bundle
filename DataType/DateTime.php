<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 12:26
 */
namespace GepurIt\ReportBundle\DataType;

use GepurIt\ReportBundle\Exception\ReportDataTypeException;
use Yawa20\RegistryBundle\Registrable\RegistrableInterface;

/**
 * Class DateTime
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class DateTime implements ReportDataTypeInterface, RegistrableInterface
{
    const NAME = 'datetime';

    /**
     * @var string $typeId
     */
    protected $typeId;

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

    /**
     * @return string
     */
    public function getKey(): string
    {
        return self::NAME;
    }
}
