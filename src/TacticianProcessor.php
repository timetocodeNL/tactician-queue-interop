<?php

namespace Enqueue\Tactician;

use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;
use League\Tactician\CommandBus;

class TacticianProcessor implements Processor
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Message $message, Context $context)
    {
        $this->commandBus->handle(new ReceivedMessage($message));

        return self::ACK;
    }
}
