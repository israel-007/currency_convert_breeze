<?php

include 'vendor/autoload.php';

use converter\converter;

$converter = new converter();

$usdToEur = $converter->from('USD')
                        ->to(['NGN', 'GBP', 'php'])
                        ->run('json');

echo ($usdToEur);