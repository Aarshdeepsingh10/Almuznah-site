
<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/razorpay_config.php';

use Razorpay\Api\Api;

try {
    $api = new Api($keyId, $keySecret);

    echo "Razorpay SDK loaded successfully.";
} catch (Exception $e) {
    echo "Razorpay setup failed.";
}
