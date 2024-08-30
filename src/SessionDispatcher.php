<?php
namespace Uniscale\PrivateDemoPhpBackend\SessionDispatcher;

use Uniscale\Platform\Platform;
use function Uniscale\PrivateDemoPhpBackend\Services\getAccountInterceptors;
use function Uniscale\PrivateDemoPhpBackend\Services\getMessagesInterceptors;

global $platformSession;

function initializePlatformSession() {
    global $platformSession;
    $platformSession = Platform::builder()
        ->withInterceptors(function($builder) {
            getAccountInterceptors($builder);
            getMessagesInterceptors($builder);
        })
        ->build();
}
