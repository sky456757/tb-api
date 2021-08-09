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
echo 'server is';
define('CONFIG_BR', 320);
define('CONFIG_URL', 'https://tb-net-api.herokuapp.com/api.php');
header("Access-Control-Allow-Origin: *");
echo 'server is';
require '/src/Meting.php';
use Metowolf\Meting;
echo 'server is';
$server = $_GET['server']??'';
$type = $_GET['type']??'';
$id = $_GET['id']??'';
echo $server;
if (empty($id)) {
    die('[]');
}
if (!in_array($server, ['netease','tencent','baidu','xiami','kugou'])) {
    die('[]');
}
if (!in_array($type, ['song','album','search','artist','playlist','lrc','url','pic'])) {
    die('[]');
}

$api = new Meting($server);
$api->format(true);
if ($server == 'netease') {
    $api->cookie('WM_NI=mwySEllsRW03RdXZYH6buMINpqUsUG5Oh1hSJrsMzoZCLSLI1SzEJwQVupaMTAqRvbaRdMFyBmcxoEcVWbmelHk7%2FVEgnl09HtNtxLba82JOXfZrmrbIxVDFc8wI3wN%2FMkU%3D; WM_NIKE=9ca17ae2e6ffcda170e2e6eed8d65aabe9be83c241ab9a8eb7d85a829f8eaeb666b5eea0b5d367b79de594c82af0fea7c3b92aafa9b7b4c868b0ad9eb8b33992bc98bbdc44f6bebbaecc46b4af9b94d84df791a8d2eb598aaebdaef9659a9d84b4cf43f38db6a6c97d938abb91e447989fbfb3f56aa38eacaec57382b3ad99ee4fa18a9983f06fb098a5d8ae5e87b9a28db3338cac8295fb48a2bff8d3c9459cba9693f07aa297b8b7c74ea1e8b9aab27d8ef1838cd837e2a3; WM_TID=CzeJQRKdwf1EQEQEBUd7hEKlpv%2B4x2Vj; ntes_kaola_ad=1; JSESSIONID-WYYY=FvjxnpVGYJZS7Dw%2FcytMqHlDx%2FznSwIDnwdyhZdb9y0OFQqACPXiadbTbktE8eGiXJSor%2FVG7NYNeWpZ2as66U115lG1yklmTk%5CfnqS2dgFmawp9JbOltGusloqXn8VkG4Kchtdb%5C3lZy%5C%2Fuy6KjjjoP%2FZBI4bpxxfDQt5aH0WWndH7a%3A1628518900240; _iuqxldmzr_=32; MUSIC_U=0ff5dbc42a8584a6724f88933a606bcfe82562bbf8998e103d6097dda406efbdb6834a44ed771c0ade39c620ce8469a8; __csrf=5a0de65cdecb1d872dd1db619c1517e0; __remember_me=true; playerid=46416674; csrfToken=4f2eee-cU8xDeYdOCtKzz0Tk; NMTID=00OhNf-8mvXzjF0zU3zl2qn1HbganEAAAF2QD7UuQ; _ntes_nnid=902bfc0b12dfcb88bef806810b7dc002,1607395627876; _ntes_nuid=902bfc0b12dfcb88bef806810b7dc002');
}

if ($type=='lrc') {
    $data=$api->lyric($id);
    $data=json_decode($data, true);
    header("Content-Type: application/javascript");
    echo lrctran($data['lyric'], $data['tlyric']);
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
