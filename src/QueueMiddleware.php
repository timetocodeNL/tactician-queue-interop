<?php

namespace Enqueue\Tactician;

use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpQueue;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Producer;
use Interop\Queue\Queue;
use League\Tactician\Middleware;

final class QueueMiddleware implements Middleware
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var bool
     */
    private $isQueueDeclared;

    /**
     * @param Context $context
     * @param string $queueName
     * @param bool $isQueueDeclared
     */
    public function __construct(Context $context, $queueName, $isQueueDeclared = false)
    {
        $this->context = $context;
        $this->queueName = $queueName;
        $this->isQueueDeclared = $isQueueDeclared;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        if ($command instanceof Message) {
            $this->context->createProducer()->send($this->createQueue(), $command);

            return $next(new QueuedMessage($command));
        }

        return $next($command);
    }

    /**
     * @return Queue
     */
    protected function createQueue()
    {
        $queue = $this->context->createQueue($this->queueName);

        if ($queue instanceof AmqpQueue) {
            /** @var AmqpContext $context */
            $context = $this->context;

            $queue->addFlag(AmqpQueue::FLAG_DURABLE);

            if (false == $this->isQueueDeclared) {
                $context->declareQueue($queue);

                $this->isQueueDeclared = true;
            }
        }

        return $queue;
    }

    /**
     * @return Producer
     */
    protected function createProducer()
    {
        return $this->context->createProducer();
    }
}
