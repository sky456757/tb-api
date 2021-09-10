<?php
$path = dirname(__FILE__);
$file = file($path."/1.txt");
$line = mt_rand(0, count($file)-1);
$url = trim($file[$line]);
if (isset($url))
{
Header("HTTP/1.1 303 See Other");
Header("Location: $url");
exit;
}
?>
?>