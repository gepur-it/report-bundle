<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since: 24.11.17
 */

namespace GepurIt\ReportBundle\CreateCommand;

/**
 * Class CreateCommandMessage
 * @package CreateCommand
 */
class CreateCommandMessage
{
    /**
     * CreateCommandMessage constructor.
     * @param \stdClass|null $incoming
     */
    public function __construct(\stdClass $incoming = null)
    {
        if (null == $incoming) {
            return;
        }
        $this->commandId = $incoming->commandId;
        $this->commandClass = $incoming->commandClass;
    }

    /**
     * @var string
     */
    public $commandClass;

    /**
     * @var string
     */
    public $commandId;
}
