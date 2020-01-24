<?php
namespace Enqueue\Tactician;

use Interop\Queue\Message;

/**
 * Indicates that the message has been sent to a MQ broker.
 */
final class QueuedMessage
{
    /**
     * @var Message
     */
    private $message;

    /**
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
