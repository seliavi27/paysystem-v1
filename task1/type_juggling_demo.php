<?php
declare(strict_types=1);

// Неявное преобразование типов
$str = "100";
$a = 50;
$result = $str + $a;
echo "Неявное преобразование типов: " . "$result" . "</br>";

// Явное преобразование типов
$str = "100";
$a = 50;
$result = $a + (boolean)$str;
echo "Явное преобразование типов: " . "$result" . "</br>";

// Строки, которые начинаются с цифр
$str = "99 bottles";
$result = (int)$str;
echo "Строки, которые начинаются с цифр: " . "$result" . "</br>";

// Опасные преобразования
$str = "0";
$a = false;
if ($str == $a)
{
    echo "Опасные преобразования: " . '"0" == false' . "</br>";
}

// Разница между `strict_types = 1` и без него
function strict(int $value): int
{
    return $value * 2;
}
$var = 5;
$strVar = "5";

$result = strict($var);
echo "С `strict_types = 1`: " . "$result" . "</br>";

$result = strict((int)$strVar);
echo "С `strict_types = 1`: " . "$result" . "</br>";
