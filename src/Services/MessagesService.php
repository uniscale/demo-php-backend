<?php

namespace Uniscale\PrivateDemoPhpBackend\Services;

use Uniscale\Platform\Platform;
use Uniscale\PrivateDemoPhpBackend\Service;
use Uniscale\Uniscaledemo\Messages\Messages\MessageFull;

/**
 * MessagesService handles requests for the Messages service.
 *
 * This service is responsible for managing and processing messages,
 * including storing, retrieving, and manipulating messages.
 */
class MessagesService extends Service
{
    /**
     * @var array<string, MessageFull> $messages
     *
     * An associative array where the key is the message identifier (UUID as string)
     * and the value is the corresponding MessageFull object.
     */
    private array $messages = [];

    /**
     * Constructor initializes the MessagesService with a specific port and service name.
     */
    public function __construct()
    {
        parent::__construct(5192, 'Messages');
    }

    /**
     * Initializes the session and registers interceptors for message-related functionalities.
     *
     * This method sets up interceptors that handle the sending, listing, and processing of messages
     * within the service.
     *
     * @return void
     */
    protected function initializeSession(): void
    {
        // Register the timeline functionality interceptor with access to the forwarding session
        $this->session = Platform::builder()
            ->withInterceptors(
                fn($i) => TimelineInterceptors::registerInterceptors($i, $this->messages))
            ->build();
    }
}