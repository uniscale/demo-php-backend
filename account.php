<?php
require __DIR__ . '/vendor/autoload.php';

use Uniscale\PrivateDemoPhpBackend\Services\AccountService;

$service = new AccountService();
try {
    $service->start();
} catch (Exception $e) {
    echo $e->getMessage();
}