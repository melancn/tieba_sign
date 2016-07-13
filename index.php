<?php
session_start();
include_once('config.php');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>首页</title>
<link rel="stylesheet" type="text/css" href="file/style.css" />
<link rel="stylesheet" type="text/css" href="file/index.css" />
</head>
<body>
<div class="top">
	<h1><strong><?php echo $config['index']['title']?></strong> <?php echo $config['index']['version']?></h1>
	<p><?php echo $config['index']['author']?></p>
</div>
<div class="main">
<?php 
if(!empty($config['login']['username']))echo '<fieldset><h2 align="center">用户登录</h2><form name="LoginForm" method="post" action="login.php"><table><tbody><tr><td>用户名</td><td><input id="username" name="username" type="text" class="input"></td></tr> <tr><td>密码</td><td><input id="password" name="password" type="password" class="input"></td></tr></tbody></table><input type="submit" name="submit" value="登录"></form></fieldset>';
else echo '<a href="site.php"><button>设置用户</button></a><a href="record.php"><button>查看记录</button></a><a href="sign.php"><button>测试签到</button></a><br><br><br><br><br>';
if(isset($_SESSION['msg'])){
	echo iconv('GB2312', 'UTF-8', urldecode($_SESSION['msg']));
	unset($_SESSION['msg']);
}
?>  
</div>
</body>
</html>
