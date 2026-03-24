<?php

define('COMMISSION_PERCENT', 2.5);

class Currency
{
    const string CURRENCY = "RUB";
}

echo "COMMISSION_PERCENT = " . COMMISSION_PERCENT . "</br>";
echo "CURRENCY = " . Currency::CURRENCY . "</br>";
echo "__LINE__ = " . __LINE__ . "</br>";
echo "__FILE__ = " . __FILE__ . "</br>";