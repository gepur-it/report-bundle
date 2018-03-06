<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 04.01.18
 * Time: 18:53
 */

namespace GepurIt\ReportBundle\ReportType;

use GepurIt\ReportBundle\Exception\ReportException;

/**
 * Class ReportTypeRegistry
 * @package ReportBundle\ReportType
 */
class ReportTypeRegistry
{
    /**
     * @var ReportTypeInterface[]
     */
    private $registered = [];

    /**
     * @param ReportTypeInterface $type
     */
    public function register(ReportTypeInterface $type): void
    {
        $this->registered[$type->getTypeId()] = $type;
    }

    /**
     * @param string $typeId
     * @return ReportTypeInterface
     */
    public function get(string $typeId) :ReportTypeInterface
    {
        if (false === array_key_exists($typeId, $this->registered)) {
            throw new ReportException('The report type with id='.$typeId.' not exists');
        }

        return $this->registered[$typeId];
    }

    /**
     * @return array
     */
    public function getTypes() :array
    {
        return $this->registered;
    }
}
