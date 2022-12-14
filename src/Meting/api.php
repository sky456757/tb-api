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
    $api->cookie('SESSIONID-WYYY=vhRgXF%2B0eNn8vG%5ChX3K7z7oq9me%2FUtl7vzJnhuNa4kZ%5CjY%2FCpJcathV9cwKVwP16HP6Y2TK02GHzih8%5C0fJoXMl0xaWayT8O64yQqY65gEm1PGHQ03U%2Bshgta08%2BhhmtrYSiH1nedR0VcSbtqFcgTDoNoMbS5ecIeCHv4P1Ss0e8FEm7%3A1671021803989; _iuqxldmzr_=32; _ntes_nnid=5be693430b46f637e33a08cb4e215956,1671020004063; _ntes_nuid=5be693430b46f637e33a08cb4e215956; NMTID=00OBcLqlcqlsn7dT0s_mIM3OEe1CM0AAAGFEI3EMw; WEVNSM=1.0.0; WNMCID=uqplqi.1671020004730.01.0; WM_NI=l%2BrM9pNvcwrNMMCPyRf%2FNmFJh0o2TgzAQ3Hfn5Q6gLHXxIyBIoE4LyBWmd%2BWG%2B8aztDDC5rLMR70u8OHCreBSGZtFQnc3WIMViE3Mxj%2BdpsbY%2BzdRrhoPjzLgzffLseAREE%3D; WM_NIKE=9ca17ae2e6ffcda170e2e6eeb9e27dabbabba8b54b8aa88ab7c45a968b9f83c152f4939f88e16bacb2b897f72af0fea7c3b92ab0f5beaebc7c8eaaad98f1468f938db1fc5aa6f1828ec542acf0a1d3d76dba9f9fd4cb6eaeeb9fd7e6488c88b8aafc4a8bb18bd5cf738289a385aa5d85f5f882f980889c8dd6f1488f8ef9d1f8348d988d93e84a91999695c55baebdfdb8cc4796b78bd4d074aaf5ac91f26a9bf5b685d17aacb1a08bf948a5a6aa87cf79a79f9ea6c837e2a3; WM_TID=uGrK0NDv1Z9FVUQVFEaVdxZU1fLlil65');
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
