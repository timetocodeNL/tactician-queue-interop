<?php

namespace Enqueue\Tactician;

use Interop\Queue\Context;
use League\Tactician\Middleware;

final class NormalizerMiddleware implements Middleware
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param Serializer $serializer
     * @param Context $context
     */
    public function __construct(Serializer $serializer, Context $context)
    {
        $this->serializer = $serializer;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        if ($command instanceof QueueMessage) {
            return $next($this->context->createMessage($this->serializer->serialize($command->getMessage())));
        }

        if ($command instanceof ReceivedMessage) {
            $serializedCommand = $command->getMessage()->getBody();

            return $next($this->serializer->deserialize($serializedCommand));
        }

        return $next($command);
    }
}
