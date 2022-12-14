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
    die('');
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
    $api->cookie('os=pc; osver=Microsoft-Windows-10-Professional-build-10586-64bit; appver=2.10.6.200601; MUSIC_U=0025128F02723CA66D0B958AB332B536687963B3E984FAF326FCC0B56F47AF00975A39B4C3242C9AFBCA32FE7BAA57063DD01F17F10A8C40482F417355BDE2B25779F19B37B4DA6C13E85AEA379ECCAB04F75CBFDB9D936B45537514CB29FEAD4D61BFAC2DCF3165F860871369B9C5A742690175E6836EA2A55C926F53F5B2CCB3CD74C78F3F862D73A9C0A4DCF2CD90545FA0540C303011ACA17EDF14F34A834AEEAA41F24524B748404DBD91E2F85F8A69D221F09B89DD90D0B6754CBF66B9310FF5E53163C853AF20E7AB6F2849E872171E468E78DA8451E4E6B923EF670ECB25A7C71A8FD6075EC92F3ED9FBF84141A596B7BC248600574EBA720B274D28035C0FDCA3EBF0D9776440DCAF6C0EACF2B43C913DC9D3A64C7C30445BB0FF3ABE203CF30FD21D1BAFFCC7E8C40B4436C49229707A625ECF082F6BB85A5720724E6B0936D48B4863C6040ADA7EC403B28BDFCA2A277F689319327110B840CF7FA8; channel=netease;  __remember_me=true');
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
