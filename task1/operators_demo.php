<?php
$a = 100;
$b = 50;
$c = 99;
$d = "99 bottles";
$pow = 3;
$eNull = null;
$f = 0;

// +
$sumAB = $a + $b;
echo "$a + $b = " . "$sumAB" . "</br>";

// **=
$cPow = $c;
$cPow **= $pow;
echo "$c **= $pow = " . "$cPow" . "</br>";

// ? :
$ter = ($b > $c) ? $b : $c;
echo "$b > $c : $b ? $c --- " . "$ter" . "</br>";

// <=>
$compare = $b <=> $a;
echo "$b <=> $a --- " . "</br>";

if ($compare == 0)
{
    echo "$b и $a равны" . "</br>";
}
elseif ($compare == -1)
{
    echo "$b меньше $a" . "</br>";
}
elseif ($compare == 1)
{
    echo "$b больше $a" . "</br>";
}


// &&
$min = 0;
$max = 10;
$current = 4;

if (($current >= $min) && ($current <= $max))
{
    echo "$current в допустимом диапазоне" . "</br>";
}

// ??
$f = $eNull ?? 1;
echo "NULL ?? 1 = " . "$f" . "</br>";
