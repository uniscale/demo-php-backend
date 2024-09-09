<?php

namespace Uniscale\PrivateDemoPhpBackend\Services;

use DateTime;
use Ramsey\Uuid\Uuid;
use Uniscale\Http\Result;
use Uniscale\Platform\PlatformInterceptorBuilder;
use Uniscale\Uniscaledemo\Messages\Messages\Empty_;
use Uniscale\Uniscaledemo\Messages\Messages\MessageFull;
use Uniscale\Uniscaledemo\Messages\Messages\SendMessageInput;
use Uniscale\Uniscaledemo\Messages\Messages\UserTag;
use Uniscale\Uniscaledemo\Messages_1_0\ErrorCodes;
use Uniscale\Uniscaledemo\Messages_1_0\Functionality\Servicetomodule\Messages\Timeline\Listmessages\GetMessageList;
use Uniscale\Uniscaledemo\Messages_1_0\Functionality\Servicetomodule\Messages\Timeline\Sendmessage\SendMessage;
use Uniscale\Utilisation\Types\FeatureContext;

/**
 * TimelineInterceptors registers and handles message-related interceptors for the Messages service.
 *
 * This class is responsible for setting up interceptors that manage
 * operations such as sending messages and retrieving a list of messages.
 */
class TimelineInterceptors
{
    /**
     * Registers interceptors for handling message operations.
     *
     * @param PlatformInterceptorBuilder $builder The builder to which interceptors are added.
     * @param array<string, MessageFull> $messages Reference to the messages array used by the service.
     * @return void
     */
    public static function registerInterceptors(PlatformInterceptorBuilder $builder, array &$messages): void
    {
        $builder->interceptMessage(
            SendMessage::FEATURE_ID,
            SendMessage::handle(function (SendMessageInput $input, FeatureContext $ctx) use (&$messages) {
                // Validate message length
                if (strlen($input->message) < 3 || strlen($input->message) > 60) {
                    return Result::badRequest(ErrorCodes::$messages->invalidMessageLength);
                }

                // Create a new message
                $msg = new MessageFull();
                $msg->messageIdentifier = Uuid::uuid4();
                $msg->message = $input->message;
                $tag = new UserTag();
                $tag->by = $input->by;
                $tag->at = new DateTime();
                $msg->created = $tag;

                // Store and return
                $messages[$msg->messageIdentifier] = $msg;
                return Result::ok();
            }));
        $builder->interceptRequest(
            GetMessageList::FEATURE_ID,
            GetMessageList::handle(function (Empty_ $input, FeatureContext $ctx) use (&$messages) {
                $response = self::getMessages($messages);

                return Result::ok($response);
            })
        );
    }

    /**
     * Returns 50 messages in descending order by their created timestamp.
     *
     * @param array<string, MessageFull> $messages An associative array of MessageFull objects keyed by string identifiers.
     * @return array<MessageFull> An array of the first 50 messages sorted in descending order by their created timestamp.
     */
    public static function getMessages(array $messages): array
    {
        // Step 1: Convert the associative array to a regular array of MessageFull objects
        $messageArray = array_values($messages);

        // Step 2: Sort the messages by the created timestamp in descending order
        usort($messageArray, function (MessageFull $a, MessageFull $b) {
            $aTime = $a->created->at->getTimestamp() ?? 0;
            $bTime = $b->created->at->getTimestamp() ?? 0;
            return $bTime <=> $aTime;
        });

        // Step 3: Get the first 50 messages
        return array_slice($messageArray, 0, 50);
    }
}