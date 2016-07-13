<?php
include('my.php');
include('connect.php');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置用户</title>
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="file/style.css" />
<link rel="stylesheet" type="text/css" href="file/record.css" />
</head>
<body>
<div class="top">
	<h1><strong><?php echo $config['index']['title']?></strong> 用户记录</h1>
	<p><?php echo $config['index']['author']?></p>
</div>
<div class="main">
	<h2>用户状态</h2>
	<div class="br">
		<a href="site.php"><button>设置用户</button></a>
		<a href="sign.php"><button>测试签到</button></a>
		<a href="login.php?action=logout"><button>登出</button></a>
	</div>
	<table><tbody>
		<tr>
			<th>代号</th>
			<th>COOKIE状态</th>
			<th>最近完成签到时间</th>
			<th>最近获得贴吧个数</th>
			<th>当前剩余签到的吧</th>
			<th>还不支持签到的吧</th>
		</tr>
    <?php 
    $result = $pdo->query('SELECT count(*) as c FROM sign_notes')->fetch();
    $count = $result['c'];
	$result = $pdo->query('SELECT cname,ct,end_sign,tnum,uid FROM sign_notes');
    $cookiestatus = array(1=>'有效',2=>'无效');
    while($r = $result->fetch()){
        $res = $pdo->query('SELECT COUNT(*) as c FROM tieba_list WHERE uid='.$r['uid'])->fetch();
        $remain = $res['c'];
        $res = $pdo->query('SELECT COUNT(*) as c FROM history_notes WHERE uid='.$r['uid'].' AND type = 3')->fetch();
        $notsign = $res['c'];
		echo '<tr><td>'.$r['cname'].'</td><td>'.$cookiestatus[$r['ct']].'</td><td>'.$r['end_sign'].'</td><td>'.$r['tnum'].'</td><td>'.$remain.'</td><td>'.$notsign.'</td></tr>';
	}
    if(!$count)echo "<tr><th colspan=6>暂无用户</th></tr>";
	else echo "<tr><th colspan=6>共".$count."个用户</th></tr>";?>
	</tbody></table>
</div>


<script src="script.js"></script>
</body>
</html>