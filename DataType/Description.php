<?php
/**
 * @author Marina <m934222258@gmail.com>
 * @since 13.11.19
 */

namespace GepurIt\ReportBundle\DataType;

use Yawa20\RegistryBundle\Registrable\RegistrableInterface;

/**
 * Class StringData
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class Description implements ReportDataTypeInterface, RegistrableInterface
{
    const NAME = 'description';

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
        return (string)$data;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return self::NAME;
    }
}
