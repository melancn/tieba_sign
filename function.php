<?php
function curl_get($url,$bduss,$ua=false){
	$cookie="BDUSS=$bduss";
    $ch=curl_init($url);
    if ($ua){
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: bdtb for Android 6.8.3'));
    }else{
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0','Connection:keep-alive','Referer:http://wapp.baidu.com/'));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_COOKIE,$cookie); 
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}
function curl_post($pda,$url="http://c.tieba.baidu.com/c/c/forum/sign"){
	$header = array(
        'Content-Type: application/x-www-form-urlencoded',
        'User-Agent: bdtb for Android 6.8.3',
    );
    $p = array (
      '_client_id' => 'wappc_1468200240475_882',
      '_client_type' => '2',
      '_client_version' => '6.8.3',
      '_phone_imei' => '140706193242534',
      'cuid' => strtoupper(md5('123')).'|0',
      'from' => '1316a',
      'model' => 'NX503A',
      'stErrorNums' => '1',
      'stMethod' => '1',
      'stMode' => '1',
      'stTime' => '442',
      'stTimesNum' => '1',
      'timestamp' => time().'123',
    );
    $pda = array_merge($p,$pda);
    ksort($pda);
    foreach($pda as $k => $v){
        $u[] = $k.'='.urlencode($v);
        $s[] = $k.'='.$v;
    }
	$data = implode("&", $u)."&sign=".md5(implode("", $s)."tiebaclient!!!");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$re = curl_exec($ch); 
	curl_close($ch);
	return $re;
}

function testcookie($cookie){
    preg_match('/BDUSS=(.+?);/', $cookie, $matches);
    if(!$matches[1]) exit('{"msg":"cooike错误,未提交","no":1}');
    $islogin = "http://tieba.baidu.com/dc/common/tbs?t=".time();
    $check = json_decode(curl_get($islogin,$cookie));
    if (!$check->is_login){
		echo '{"msg":"cooike错误,未提交","no":1}';
        exit();
    }
    $mylikeurl="http://c.tieba.baidu.com/c/f/forum/like";
    $pda = array('BDUSS' => $matches[1]);
    $result = curl_post($pda,$this->mylikeurl);
    $jsonobj = json_decode($result);
    $i = count($jsonobj->forum_list);
    return $i;
}