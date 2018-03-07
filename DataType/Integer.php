<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 23.01.18
 * Time: 13:14
 */

namespace GepurIt\ReportBundle\DataType;

use Yawa20\RegistryBundle\Registrable\RegistrableInterface;

/**
 * Class Integer
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class Integer implements ReportDataTypeInterface, RegistrableInterface
{
    const NAME = 'integer';

    /**
     * @var string $typeId
     */
    protected $typeId;

    /**
     * @param mixed $data
     * @return int
     */
    public function process($data)
    {
        return (int)$data;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return self::NAME;
    }
}
