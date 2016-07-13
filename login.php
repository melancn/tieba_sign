<?php
session_start();
include('config.php');
if(empty($config['login']['username'])){
    header("Location:index.php");
    exit();
}

//注销登录
if(isset($_GET['action']) && $_GET['action'] == "logout"){
	unset($_SESSION['username']);
	header("Location:index.php");
	exit();
}

if(strtoupper($_SERVER['REQUEST_METHOD']) != 'POST'){
	header("Location:index.php");
	exit('非法访问!');
}

if(!strcmp($_POST['username'],$config['login']['username']) && !strcmp($_POST['password'],$config['login']['password'])){
	$_SESSION['username'] = $config['login']['username'];
	header("Location: site.php");
	exit();
}else {
	$_SESSION['msg']='<p align="center">用户名或者密码错误</p>';
	header("Location:index.php");
}
