<?php
/**
 * @author Marina <m934222258@gmail.com>
 * @since 13.11.19
 */

namespace GepurIt\ReportBundle\DataType;

use Yawa20\RegistryBundle\Registrable\RegistrableInterface;

/**
 * Class Upload
 * @package ReportBundle\ReportType\ReportDataTypes
 */
class Upload implements ReportDataTypeInterface, RegistrableInterface
{
    const NAME = 'upload';

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
