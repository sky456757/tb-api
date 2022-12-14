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
    $api->cookie('WEVNSM=1.0.0; ntes_kaola_ad=1; NTES_P_UTID=TcdmfEWY22hIMRg1RXGjSoHWTkYqSmee|1671023434; NTES_SESS=K7UUxNGC3R_ABl32ig04Nk5q2ZCohWta_Q3kEekSBzzsY6e5r0OfIIruOaQRC6Gb2D0suegCNJgAgNVeRXXzrQbprIyuJdkC6.Z1uKvQgAkylWn18GvGdYNwu7v39G_phLy6g9Lp8LmfwIBxmPs6g2R83XC14nJop7jfRC5zcSEq_5sNZjacgOqpgQTCkOBuMLW4584mznwxj; P_INFO=sky456757@163.com|1671023434|0|music|00&99|taiwan&1669217831&mail163#taiwan&710000#10#0#0|&0||sky456757@163.com; S_INFO=1671023434|0|3&80##|sky456757; MUSIC_U=0025128F02723CA66D0B958AB332B536687963B3E984FAF326FCC0B56F47AF00975A39B4C3242C9AFBCA32FE7BAA57063DD01F17F10A8C40482F417355BDE2B25779F19B37B4DA6C13E85AEA379ECCAB04F75CBFDB9D936B45537514CB29FEAD4D61BFAC2DCF3165F860871369B9C5A742690175E6836EA2A55C926F53F5B2CCB3CD74C78F3F862D73A9C0A4DCF2CD90545FA0540C303011ACA17EDF14F34A834AEEAA41F24524B748404DBD91E2F85F8A69D221F09B89DD90D0B6754CBF66B9310FF5E53163C853AF20E7AB6F2849E872171E468E78DA8451E4E6B923EF670ECB25A7C71A8FD6075EC92F3ED9FBF84141A596B7BC248600574EBA720B274D28035C0FDCA3EBF0D9776440DCAF6C0EACF2B43C913DC9D3A64C7C30445BB0FF3ABE203CF30FD21D1BAFFCC7E8C40B4436C49229707A625ECF082F6BB85A5720724E6B0936D48B4863C6040ADA7EC403B28BDFCA2A277F689319327110B840CF7FA8; __csrf=7a0671b0b821b7404491653b80dffa59; __remember_me=true; WM_TID=BLykjVOi%2FSxFAUBURRKANxZAgbPDZHMN; JSESSIONID-WYYY=%5Cf6H9m9bkyK1oHJW3DmcY5XQcVyWAoHAFeT4lVJ6tvUXCS4U%2Fzvmf3HwZkjnhcOIJPO4eEXf9zSRKRq%5Cu2Jk02NCxIoa1PoJw22aCjXclzRVi1zJm7iNP6WQ%2F3%2FzHcdDszsFTpN5yjQKi5oimhzRveBZ5NoU8v4cuAh5Mr182%5C%2B%2FVnjz%3A1671025223192; _iuqxldmzr_=33; YD00000558929251%3AWM_NI=iHCUVuiNcNRw6ULnm2xlhjikkU2ZX3JUh%2BESbMXXtMl03SyAgHVjDG8aq4bo8Q2HHWHijprYezFOquU%2BZXTm3owCA6WZJ9NFz%2Bl20d6NimW%2Bow%2FVyGFaTmUYemdrUoBSTFU%3D; YD00000558929251%3AWM_NIKE=9ca17ae2e6ffcda170e2e6eed7ef60f7b1acb6d166fcb88fa6c55f939f9aacd146f4929a91d545b29efe9af22af0fea7c3b92aab988db6d57fab92adafd17df68b848ff03abcb4a88bec61a98bfebbdb398a8ba8ccc859a29398b9f747b0f0fdb4fc4ba5989a8bc74dfcbfbfd5e165fc8ffea9cb5d8cb7a691f153a7a68ab0f47f97f596b9d767f1b1b9a4c43e958686dacd4ebaf5a4d3b370f692a78ed621ada9e1b7fc4d86bf9c8ed853a7b3ac93d865828d9ca7d837e2a3; YD00000558929251%3AWM_TID=wCs%2BxcP27jREBUAQRQLFM1JB5ALrUnio; __snaker__id=Ta88Gm5g1mIkLWmE; gdxidpyhxdE=JY6535p7tw1MjUHbf4vd71v2umpfJheKJWH4fIuqLgU1%2B76vE3dTdCVbII%5Cr5asgPz4NE73BmON8UTjGEuKXrymkWUWcYho5MXbLO%5ChNmVaTaNalCoat3qfdcedKvIdbayr%2FJknTCKR1q5m0qJcUPGV4lQKoXRlaL9ktS%2FNP9MPPHRPA%3A1671024323607; WM_NI=so%2B13TLATAGbNs3S%2BQ2oKlR0h5Ze2ueG9oQWIPoQ5J7Z%2BpocrKCEm24jSo7Kjf8Ni%2Fw6vHsqKgfeJX3F6bZxL0lz23S3uDIWZ4bolBm%2BoHRW9G%2B%2F61bj%2BYrfM35ZqfyoeEU%3D; WM_NIKE=9ca17ae2e6ffcda170e2e6eed0b36eb494a8badb6f8d968ba6d15f928f9eb0d869bc869a88e445888789aadc2af0fea7c3b92ab1b1e5d2b260909f9ca2c66a8eadfdb4aa5bf2b1858dd13caceb9686b17fa799f790d45d8b90a1b2b842f586e591ee69b2958da6f03af8b69d90b641aabaf8afec25b5eabaa9f27b89b9aa86c954f19afa83d97490f0a49bb13991edbba5cc5d9aecae90ef4aaff5a1a9d15dfb99e5d0b53ba2b6e5baf16e8fedfbbbf46ebbb1aba6d837e2a3; _ga=GA1.1.1070907260.1664595468; _ga_C6TGHFPQ1H=GS1.1.1669813099.2.0.1669813099.0.0.0; timing_user_id=time_ixl4FNvOQR; Qs_pv_382223=2018842586201408300; Qs_lvt_382223=1664595468; _clck=oxzoqb|1|f5c|0; WNMCID=zkyqrh.1652441598330.01.0; _ntes_nnid=902bfc0b12dfcb88bef806810b7dc002,1641716015159; _ntes_nuid=902bfc0b12dfcb88bef806810b7dc002; _9755xjdesxxd_=32; NMTID=00OhNf-8mvXzjF0zU3zl2qn1HbganEAAAF2QD7UuQ');
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
