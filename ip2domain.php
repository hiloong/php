#!/usr/bin/php

<?php
// 本程序需要在命令行下面执行 
//
// 获得的地址  http://dns.aizhan.com/index.php?r=index/domains&ip=x.x.x.x
//

if(php_sapi_name() !== 'cli') {
    die("must running in cli evn");
}

if($argc < 2) die(' ip address in null');
$ip = $argv[1];  // 
if( !filter_var( $ip, FILTER_VALIDATE_IP )) {
    die("$ip is not a ip address");
}

$api = "http://dns.aizhan.com/index.php?r=index/domains&ip=";
$url = $api.$ip;
$ans = array();

function getdomains( $max = 1) {
    // 最多获取前二十页的内容
    if($max > 10 ) return ;
    global $ans;
    global $api;
    global $url;
    global $ip;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $tmp = curl_exec( $ch );

    if( curl_errno( $ch ) ) {
        echo "Errno" . curl_error( $curl);
        exit;
    }
    curl_close( $ch );
    $tmp = json_decode($tmp, true);
    foreach ( $tmp['domains'] as $v ) { 
        $ans[] = $v;
    }
    
    if($tmp['page'] < $tmp['maxpage']) {
        $nextpage = $tmp['page'] + 1 ;
        $url = $api.$ip."&page=".$nextpage;

        getdomains( $max + 1);
    }
}
getdomains();

echo implode(" ", $ans);
