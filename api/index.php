<?php
//获取当前绝对路径
$path = dirname(__FILE__);
//读取文本文件
$file = file($path."/1.txt");
//随机选择一行作为url
$line = mt_rand(0, count($file)-1);
$url = trim($file[$line]);
if (isset($url))
{
Header("HTTP/1.1 303 See Other");
Header("Location: $url");
exit;
}
?>