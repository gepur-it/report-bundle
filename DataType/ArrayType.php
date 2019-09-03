<?php
/**
 * @author : Mari <m934222258@gmail.com>
 * @since : 03.09.19
 */
namespace GepurIt\ReportBundle\DataType;

use GepurIt\ReportBundle\Exception\ReportDataTypeException;
use Yawa20\RegistryBundle\Registrable\RegistrableInterface;

/**
 * Class ArrayType
 * @package ReportBundle\DataType
 */
class ArrayType implements ReportDataTypeInterface, RegistrableInterface
{
    const NAME = 'array';

    /**
     * @var string $typeId
     */
    protected $typeId;

    /**
     * @param mixed $data
     * @return array
     * @throws ReportDataTypeException
     */
    public function process($data)
    {
        try {
            $array = is_array($data) ? $data : json_decode($data, true);
        } catch (ReportDataTypeException $exception) {
            throw $exception;
        }

        return $array;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return self::NAME;
    }
}
