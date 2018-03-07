<?php
/**
 * @author: Andrii yakovlev <yawa20@gmail.com>
 * @since: 16.02.18
 */

namespace GepurIt\ReportBundle\Helpers;

use AMQPExchange;
use AMQPQueue;
use GepurIt\RabbitMqBundle\Rabbit;

/**
 * Class RabbitHandler
 * @package ReportBundle\ReportBundle\ReportCommandHandler
 */
class RabbitHelper
{
    const QUEUE__NAME = 'report_commands';
    const DEFERRED_QUEUE_NAME = 'report_commands_deferred';

    /** @var Rabbit  */
    private $rabbit;

    public function __construct(Rabbit $rabbit)
    {
        $this->rabbit = $rabbit;
    }

    /**
     * @return AMQPExchange
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function getExchange() :AMQPExchange
    {
        $exchangeName = RabbitHelper::QUEUE__NAME;

        $channel = $this->rabbit->getChannel();
        $deferredExchange = new AMQPExchange($channel);
        $deferredExchange->setName(RabbitHelper::DEFERRED_QUEUE_NAME);
        $deferredExchange->setType(AMQP_EX_TYPE_FANOUT);
        $deferredExchange->declareExchange();
        $deferredQueue =new AMQPQueue($channel);
        $deferredQueue->setName(RabbitHelper::DEFERRED_QUEUE_NAME);
        $deferredQueue->setArgument('x-dead-letter-exchange', $exchangeName);
        $deferredQueue->setArgument('x-message-ttl', 600000);
        $deferredQueue->declareQueue();
        $deferredQueue->bind(RabbitHelper::DEFERRED_QUEUE_NAME, $exchangeName);

        $exchange =  new AMQPExchange($channel);
        $exchange->setName($exchangeName);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->declareExchange();
        $queue = new AMQPQueue($channel);
        $queue->setName($exchangeName);
        $queue->setFlags(AMQP_DURABLE);
        $queue->setArgument('x-dead-letter-exchange', RabbitHelper::DEFERRED_QUEUE_NAME);
        $queue->declareQueue();
        $queue->bind($exchangeName, $exchangeName);

        return $exchange;
    }

    /**
     * @return AMQPQueue
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    public function getQueue(): AMQPQueue
    {
        $queue = new AMQPQueue($this->rabbit->getChannel());
        $queue->setName(RabbitHelper::QUEUE__NAME);
        $queue->setFlags(AMQP_DURABLE);
        $queue->setArgument('x-dead-letter-exchange', RabbitHelper::DEFERRED_QUEUE_NAME);
        $queue->declareQueue();

        return $queue;
    }
}
