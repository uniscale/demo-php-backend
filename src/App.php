<?php
namespace Uniscale\PrivateDemoPhpBackend\App;

use function Uniscale\PrivateDemoPhpBackend\Services\startAccountServer;
use function Uniscale\PrivateDemoPhpBackend\Services\startMessagesServer;

function app() {
    initializePlatformSession();
    startAccountServer();
    startMessagesServer();
}
