<?php
/*!
 * Meting music framework - self hosted API
 * https://i-meto.com
 * https://github.com/metowolf/Meting
 * Version 0.1.0
 *
 * Copyright 2017, METO Sheel <i@i-meto.com>
 * Released under the MIT license
 */
ini_set('display_errors','off');
define('CONFIG_BR', 320);
define('CONFIG_URL', 'https://tb-api.vercel.app');
header("Access-Control-Allow-Origin: *");
require 'Meting.php';
$server = $_GET['server']??'';
$type = $_GET['type']??'';
$id = $_GET['id']??'';
if (empty($id)) {
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
}
if (!in_array($server, ['netease','tencent','baidu','xiami','kugou'])) {
    die('[]');
}
if (!in_array($type, ['song','album','search','artist','playlist','lrc','url','pic'])) {
    die('[]');
}

$api=new \Metowolf\Meting($server);
$api->format(true);
if ($server == 'netease') {
    $api->cookie('os=pc; osver=Microsoft-Windows-10-Professional-build-10586-64bit; appver=2.9.1.199099; channel=netease; MUSIC_U=0ff5dbc42a8584a6724f88933a606bcfe82562bbf8998e103d6097dda406efbdb6834a44ed771c0ade39c620ce8469a8; __remember_me=true');
}

if ($type=='lrc') {
    $data=$api->lyric($id);
    $data=json_decode($data, true);
    header("Content-Type: application/javascript");
    echo $data['lyric'];
} elseif ($type=='pic') {
    $data=$api->pic($id, 90);
    $data=json_decode($data, 1);
    header('Location: '.$data['url']);
} elseif ($type=='url') {
    $data=$api->url($id, CONFIG_BR);
    $data=json_decode($data, true);
    $url=$data['url'];

    // if($server=='netease'){
    //     $url=str_replace([
    //         'http://m7.','http://m7c.',
    //         'http://m8.','http://m8c.',
    //     ],'https://m9.',$url);
    //     $url=str_replace('http://m10.','https://m10.',$url);
    // }
    
    if (!empty($data['url'])) {
        header('Location: '.$url.'#'.$data['br']);
    } else {
        header('Location: https://static.i-meto.com/static/music/empty.mp3');
    }
} else {
    $data=$api->$type($id);
    $data=json_decode($data, 1);

    $music=[];
    foreach ($data as $vo) {
        $music[]=array(
            'title'  => $vo['name'],
            'author' => implode(' / ', $vo['artist']),
            'url'    => CONFIG_URL.'?server='.$vo['source'].'&type=url&id='.$vo['url_id'],
            'pic'    => CONFIG_URL.'?server='.$vo['source'].'&type=pic&id='.$vo['pic_id'],
            'lrc'    => CONFIG_URL.'?server='.$vo['source'].'&type=lrc&id='.$vo['lyric_id'],
        );
    }
    header("Content-Type: application/javascript");
    echo json_encode($music);
}

function lrctrim($lyrics)
{
    $result="";
    $lyrics=explode("\n", $lyrics);
    $data=array();
    foreach ($lyrics as $lyric) {
        preg_match('/\[(\d{2}):(\d{2}\.?\d*)]/', $lyric, $lrcTimes);
        $lrcText=preg_replace('/\[(\d{2}):(\d{2}\.?\d*)]/', '', $lyric);
        if (empty($lrcTimes)) {
            continue;
        }
        $lrcTimes=intval($lrcTimes[1])*60000+intval(floatval($lrcTimes[2])*1000);
        $lrcText=preg_replace('/\s\s+/', ' ', $lrcText);
        $lrcText=trim($lrcText);
        $data[]=array($lrcTimes,$lrcText);
    }
    sort($data);
    return $data;
}

function lrctran($lyric, $tlyric)
{
    $lyric=lrctrim($lyric);
    $tlyric=lrctrim($tlyric);
    $len1=count($lyric);
    $len2=count($tlyric);
    $result="";
    for ($i=0,$j=0;$i<$len1&&$j<$len2;$i++) {
        while ($lyric[$i][0]>$tlyric[$j][0]&&$j+1<$len2) {
            $j++;
        }
        if ($lyric[$i][0]==$tlyric[$j][0]) {
            $tlyric[$j][1]=str_replace('/', '', $tlyric[$j][1]);
            if (!empty($tlyric[$j][1])) {
                $lyric[$i][1].=" ({$tlyric[$j][1]})";
            }
            $j++;
        }
    }
    for ($i=0;$i<$len1;$i++) {
        $t=$lyric[$i][0];
        $result.=sprintf("[%02d:%02d.%03d]%s\n", $t/60000, $t%60000/1000, $t%1000, $lyric[$i][1]);
    }
    return $result;
}
