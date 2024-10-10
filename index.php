<?php

include 'vendor/autoload.php';

use converter\converter;

$converter = new converter();

$usdToEur = $converter->from('cad')
                        ->to(['ngn', 'gbp', 'usd'])
                        ->amount(150)
                        ->run('json');

echo $usdToEur;