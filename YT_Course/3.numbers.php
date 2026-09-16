<?php

$x = 25;
$y = 2;

echo $x + $y;
echo "<br>";
$z = $x - $y;
echo $z;
echo "<br>";
$z = $x * $y;
echo $z;
echo "<br>";
$z = $x / $y;
echo $z;
echo "<br>";
$z = $x % $y;
echo $z;
echo "<br>";
$z = $x ** $y;
echo $z;
echo "<br>";

$y++;
echo $y.'<br>';

$y--;
echo $y.'<br>';

$y+=2;
echo $y.'<br>';
$y-=2;
echo $y.'<br>';

echo min($x, $y).'<br>';
echo max($x,$y).'<br>';

