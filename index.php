<?php
session_start();
include_once('config.php');
$title = '首页';
$stylearr = array('file/style.css','file/index.css');
include('template/header.html');
if($config['login'] && empty($_SESSION['uid']))echo '<fieldset><h2 align="center">用户登录</h2><form name="LoginForm" method="post" action="login.php"><table><tbody><tr><td>用户名</td><td><input id="username" name="username" type="text" class="input"></td></tr> <tr><td>密码</td><td><input id="password" name="password" type="password" class="input"></td></tr></tbody></table><input type="submit" name="submit" value="登录"></form></fieldset>';
else echo '<a href="site.php"><button>设置用户</button></a><a href="record.php"><button>查看记录</button></a><a href="sign.php"><button>测试签到</button></a><br><br><br><br><br>';
if(isset($_SESSION['msg'])){
	echo '<p align="center">'.$_SESSION['msg'].'</p>';
	unset($_SESSION['msg']);
}
?>  
</div>
</body>
</html>
