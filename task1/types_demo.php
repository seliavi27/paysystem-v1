<?php
declare(strict_types=1);

class User
{
    public function __construct()
    {

    }
}

$int = 1;
$float = 1.0;
$string = "1";
$bool = true;
$array = [1, 2, 3];
$arrayKey = ["a" => 'а', "b" => 'б', "c" => 'с', "d" => 'д'];
$null = null;
$object = new User;

if ($bool == $string)
{
    echo "Равны" . "</br>";
}

if ($float == $string)
{
    echo "Равны" . "</br>";
}

if ($bool == $float)
{
    echo "Равны" . "</br>";
}

if ($int == $string)
{
    echo "Равны" . "</br>";
}

if ($int !== $string)
{
    echo "Не равны" . "</br>";
}

echo '<pre>';
echo "Целое число (int): " . $int . "</br>";
echo "Дробное число (float): " . $float . "</br>";
echo "Строка (string): " . $string . "</br>";
echo "Логический тип (bool): " . $bool . "</br>";
echo "NULL (null): " . $null . "</br>";
echo "Массив (array): " . var_dump($array, true) . "</br>";
echo "Ассоциативный массив (array): " . var_dump($arrayKey, true) . "</br>";
echo "Объект (object): " . var_dump($object) . "</br>";
echo "</br>";
echo "Красивый вывод:" . "</br>";
echo "Массив (array): " . print_r($array, true) . "</br>";
echo "Ассоциативный массив (array): " . print_r($arrayKey, true) . "</br>";
echo "Объект (object): " . print_r($object) . "</br>";
echo '</pre>';
