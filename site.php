<?php
include('my.php');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>设置用户</title>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="file/style.css" />
<link rel="stylesheet" type="text/css" href="file/user.css" />
</head>
<body>
<div class="top">
	<h1><strong><?php echo $config['index']['title']?></strong> 设置用户</h1>
	<p><?php echo $config['index']['author']?></p>
</div>
<div class="main">
<div class="show">
	<h2>用户设置</h2>
	<table><tbody>
		<tr>
			<th>代号</th>
			<th>COOKIE</th>
			<th>状态</th>
			<th>操作</th>
		</tr>
		<tr><th colspan=4 class="show_wait">获取数据中</th></tr>
	</tbody></table>
</div>
<div class="help">
	<h2>定时任务</h2>
	<table>
		<tr>
			<th>次数</th>
			<th>建议时间表</th>
		</tr>
		<tr>
			<td>较少</td>
			<td class="cs">等待数据中</td>
		</tr>
		<tr>
			<td>一般</td>
			<td class="cc">等待数据中</td>
		</tr>
		<tr>
			<td>较多</td>
			<td class="cm">等待数据中</td>
		</tr>
	</table>
	<br>
	执行URL: <a href="sign.php" id="gourl"></a>
	<br>
	<br>
	<a href="record.php"><button>查看记录</button></a>
	<a href="sign.php"><button>测试签到</button></a>
	<a href="login.php?action=logout"><button>登出</button></a>
</div>
</div>
<script src="file/script.js"></script>
</body>
</html>