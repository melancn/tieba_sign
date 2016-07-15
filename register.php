<?php
session_start();
include('config.php');
if(!$config['login'] || !$config['register'] || isset($_SESSION['uid'])){
    header("Location:index.php");
    exit();
}

if(strtoupper($_SERVER['REQUEST_METHOD']) != 'POST'){
    $title = '首页';
    include('template/header.html');
?>
<fieldset>
    <h2 align="center">用户注册</h2>
    <form name="LoginForm" method="post" action="register.php">
    <table>
        <tbody>
            <tr><td>用户名</td><td><input id="username" name="username" type="text" class="input"></td></tr> 
            <tr><td>邮箱</td><td><input id="email" name="email" type="text" class="input"></td></tr>
            <tr><td>密码</td><td><input id="password" name="password" type="password" class="input"></td></tr>
        </tbody>
    </table>
    <input type="submit" name="submit" value="注册">
    </form>
</fieldset>
<?php
}else{
    //验证用户信息 TODO
    if(mb_strlen($_POST['username']) > 10) $_SESSION['msg'] = '用户名长度超出限制';
    //邮箱地址有效性验证 TODO 发送邮件验证
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $_SESSION['msg'] = '邮箱地址不正确';
    if(!empty($_SESSION['msg'])){
        header("Location:index.php");
        exit;
    }
    
    include('connect.php');

    $pre = $pdo->prepare("SELECT * FROM tieba_sign_user where username=:username or email=:email limit 1");
    $pre->bindParam(':username',$_POST['username']);
    $pre->bindParam(':email',$_POST['email']);
    $pre->execute();
    $errorInfo = $pre->errorInfo();
    if($errorInfo[0] != 0){
        $_SESSION['msg'] = $errorInfo[2];
        header("Location:index.php");
        exit;
    }
    $res = $pre->fetch();
    if(empty($res)){
        $salt = getRandChar(10);
        $pass = strtolower(sha1($res['salt'].$_POST['password']));
        $pre = $pdo->prepare("INSERT INTO tieba_sign_user set username=:username,salt=:salt,password=:password,email=:email");
        $pre->bindParam(':username',$_POST['username']);
        $pre->bindParam(':salt',$salt);
        $pre->bindParam(':password',$pass);
        $pre->bindParam(':email',$_POST['email']);
        $pre->execute();
        $errorInfo = $pre->errorInfo();
        if($errorInfo[0] != 0){
            $_SESSION['msg'] = $errorInfo[2];
        }else{
            $_SESSION['uid'] = $pdo->lastInsertId();
            //TODO 发送邮件验证有效性
        }
    }else{
        $_SESSION['uid'] = '用户已存在';
    }
    header("Location:index.php");
    
}

function getRandChar($length){
   $str = '';
   $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
   $max = strlen($strPol)-1;

   for($i=0;$i<$length;$i++){
    $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
   }

   return $str;
}