<?php
/**
 * Created by PhpStorm.
 * User: pavlov
 * Date: 04.01.18
 * Time: 18:51
 */
namespace GepurIt\ReportBundle\ReportType;

/**
 * Interface ReportTypeInterface
 * @package ReportBundle\ReportType
 */
interface ReportTypeInterface
{
    /**
     * @return string
     */
    public function getTypeId() :string;

    /**
     * @return string
     */
    public function getLabel() :string;

    /**
     * @return string
     */
    public function getResource() :string;

    /**
     * @return string
     */
    public function getRole() :string;

    /**
     * @return array
     */
    public function getCommandMeta() :array;

    /**
     * @return array
     */
    public function getReportMeta() :array;

    /**
     * @return string
     */
    public function getReportClass() :string;

    /**
     * @return string
     */
    public function getGroup() :string;
}
