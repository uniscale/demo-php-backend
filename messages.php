<?php
require __DIR__ . '/vendor/autoload.php';

use Uniscale\PrivateDemoPhpBackend\Services\MessagesService;

$service = new MessagesService();
try {
    $service->start();
} catch (Exception $e) {
    echo $e->getMessage();
}