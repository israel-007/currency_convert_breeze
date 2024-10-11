<?php

require 'vendor/autoload.php';

use converter\Converter;

try {
    $converter = new Converter([
        'basecurrency' => 'USD',  // Optional: default is 'USD'
        'defaultamount' => 100,   // Optional: default is 1
        'sessionexpiry' => 300     // Optional: caching duration in seconds
    ]);

    $result = $converter
        ->from('USD')                   // Set base currency
        ->to(['NGN', 'GBP', 'CAD'])     // Set target currencies
        ->amount(100)                   // Set amount to convert
        ->run('json');                  // Get result in JSON format

    echo $result;  // Output converted values as JSON

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}