<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since: 28.11.17
 */

namespace GepurIt\ReportBundle\Exception;

/**
 * Class GeneratorNotFoundException
 * @package Exception
 */
class GeneratorNotFoundException extends ReportException
{
    public function __construct($commandClass)
    {
        parent::__construct("No generators found for {$commandClass}");
    }
}

